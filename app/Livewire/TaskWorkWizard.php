<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\UserTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaskWorkWizard extends Component
{
    use WithFileUploads;

    public Task $task;
    public ?UserTask $userTask = null;
    public $currentStep = 1;

    // Timer for proof 1 submission (10 minutes)
    public $proof1Deadline;
    public $timeRemaining;
    public $isProof1Expired = false;

    // Step 1: Task Details & Instructions
    public $understoodInstructions = false;

    // Step 2: Upload Proof 1
    public $proof1Files = [];
    public $proof1Description = '';

    // Step 3: Upload Proof 2 (after verification 1)
    public $proof2Files = [];
    public $proof2Description = '';

    // Step 4: Completion
    public $completionNotes = '';

    protected $rules = [
        'completionNotes' => 'nullable|string',
        'understoodInstructions' => 'required|accepted',
    ];

    protected $messages = [
        'understoodInstructions.required' => 'Haraf menyetujui instruksi tasks terlebih dahulu.',
        'understoodInstructions.accepted' => 'Haraf menyetujui instruksi tasks terlebih dahulu.',
    ];

    public function mount(Task $task)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Security: Check if user is banned
        if ($user->isBanned()) {
            session()->flash('error', 'Akun Anda sedang dibanned dan tidak dapat mengakses task.');
            return redirect()->route('dashboard');
        }

        // Security: Admin tidak boleh mengakses halaman kerja task
        if ($user->isAdmin()) {
            session()->flash('error', 'Admin tidak diperbolehkan mengerjakan task.');
            return redirect()->route('dashboard');
        }

        // Load task with creator relation for admin contact info
        $this->task = $task->load('creator');

        // Get or create user task with fresh data
        // Security: ONLY get task that belongs to current user
        $this->userTask = UserTask::where('task_id', $task->id)
            ->where('user_id', Auth::id())
            ->first();

        // If no UserTask exists, redirect back to dashboard
        if (!$this->userTask) {
            session()->flash('error', 'Task tidak ditemukan atau Anda belum mengambil task ini. Silakan ambil task dari halaman dashboard.');
            return redirect()->route('user.dashboard');
        }

        // Security: Prevent access to CANCELLED (by user choice) tasks only.
        // Failed tasks (expired/rejected) are allowed to be viewed in read-only mode.
        if ($this->userTask->status === UserTask::STATUS_CANCELLED) {
            session()->forget('task_understood_' . $this->task->id);
            session()->flash('error', 'Task ini sudah dibatalkan. Silakan ambil task baru dari halaman dashboard.');
            return redirect()->route('user.dashboard');
        }

        // If user task is overdue and still active, mark as failed — then allow read-only view
        if ($this->userTask->isOverdue() && $this->userTask->isActive()) {
            $this->userTask->update([
                'status' => UserTask::STATUS_FAILED,
                'failed_count' => ($this->userTask->failed_count ?? 0) + 1,
                'cancelled_at' => now(),
                'verification_1_status' => 'Failed: Deadline passed',
            ]);
            $this->userTask->refresh();
        }

        // Calculate proof 1 deadline (10 minutes from taken_at)
        // Use copy() to avoid modifying the original taken_at timestamp
        $this->proof1Deadline = $this->userTask->taken_at->copy()->addMinutes(10);
        $this->checkProof1Timeout();

        // Check if task was rejected and should be redirected with feedback
        $isRejected = $this->isTaskRejectedAndCancelled();

        if ($isRejected) {
            // Don't redirect immediately, let user see the feedback in wizard
        }

        // Check if task was completed by admin - allow access to view completion details
        $isCompleted = $this->isTaskCompletedByAdmin();

        if ($isCompleted) {
            // Don't redirect completed tasks - let users see the completion details
            // Just set the step to show completion info
        }

        // Set current step based on status
        $this->setCurrentStepFromStatus();
    }

    public function checkProof1Timeout()
    {
        // Only check timeout if still in taken status and no proof 1 submitted yet
        if (
            $this->userTask->status === UserTask::STATUS_TAKEN &&
            !$this->userTask->verification_1_status
        ) {
            $now = now();

            if ($now->greaterThan($this->proof1Deadline)) {
                // Mark task as failed due to timeout
                $this->userTask->update([
                    'status' => UserTask::STATUS_FAILED,
                    'cancelled_at' => $now,
                    'verification_1_status' => 'Failed: Did not submit proof 1 within 10 minutes deadline. Task automatically cancelled at ' . $now->format('Y-m-d H:i:s'),
                    'failed_count' => ($this->userTask->failed_count ?? 0) + 1,
                ]);

                $this->userTask->refresh();
                $this->isProof1Expired = true;
                // Don't redirect — let the user see the expired read-only view (step 4)
                return;
            }

            // Calculate time remaining in seconds (positive value)
            $this->timeRemaining = max(0, $now->diffInSeconds($this->proof1Deadline, false));
        } else {
            // If not in taken status or proof already submitted, set time remaining to 0
            $this->timeRemaining = 0;
        }
    }

    public function canCancelTask()
    {
        // Cannot cancel if:
        // 1. Already submitted proof 1 and waiting for verification
        // 2. Proof 1 approved and in verification 2 stage
        // 3. Completed
        return !in_array($this->userTask->status, [
            UserTask::STATUS_PENDING_VERIFICATION_1,
            UserTask::STATUS_PENDING_VERIFICATION_2,
            UserTask::STATUS_COMPLETED,
        ]);
    }

    private function setCurrentStepFromStatus()
    {
        switch ($this->userTask->status) {
            case UserTask::STATUS_TAKEN:
                // If we have verification_1_status data but still taken, it means rejected
                if ($this->userTask->verification_1_status && strpos($this->userTask->verification_1_status, 'Rejected') !== false) {
                    $this->currentStep = 2; // Allow resubmission stage 1
                } else {
                    // Check if user already confirmed understanding instructions (from session)
                    $sessionKey = 'task_understood_' . $this->task->id;
                    if (session()->has($sessionKey)) {
                        // User already confirmed understanding, skip to proof upload
                        $this->currentStep = 2;
                        $this->understoodInstructions = true;
                    } else {
                        // Fallback: Check time elapsed since taking task
                        $takenAt = $this->userTask->taken_at;
                        $thirtySecondsAfterTaken = $takenAt->copy()->addSeconds(30);

                        if (now()->gt($thirtySecondsAfterTaken)) {
                            // User already spent time on this task, skip to proof upload
                            $this->currentStep = 2;
                            $this->understoodInstructions = true;
                        } else {
                            $this->currentStep = 1;
                        }
                    }
                }
                break;
            case UserTask::STATUS_PENDING_VERIFICATION_1:
                $this->currentStep = 2; // Waiting for admin approval on stage 1
                break;
            case UserTask::STATUS_PENDING_VERIFICATION_2:
                // Cek apakah sudah submit proof2 atau belum
                if ($this->userTask->verification_2_status && strpos($this->userTask->verification_2_status, 'Submitted') !== false) {
                    $this->currentStep = 3; // Sudah submit, waiting approval
                } else {
                    $this->currentStep = 3; // Bisa submit stage 2 proof
                }
                break;
            case UserTask::STATUS_COMPLETED:
                $this->currentStep = 4;
                break;
            case UserTask::STATUS_FAILED:
                // Failed tasks (expired / rejected) are shown in read-only mode on step 4
                $this->currentStep = 4;
                break;
            case UserTask::STATUS_CANCELLED:
                // Cancelled tasks redirect to dashboard (handled in mount, but as safety net)
                session()->forget('task_understood_' . $this->task->id);
                session()->flash('error', 'Task ini sudah dibatalkan. Silakan ambil task baru dari halaman dashboard.');
                return redirect()->route('user.dashboard');
            default:
                // Unknown status, redirect to dashboard for safety
                session()->flash('error', 'Status task tidak valid. Silakan ambil task dari halaman dashboard.');
                return redirect()->route('user.dashboard');
        }
    }

    public function nextStep()
    {
        $this->validateCurrentStep();

        if ($this->currentStep < 4) {
            // If moving from step 1 to 2, save understood state to session
            if ($this->currentStep === 1 && $this->understoodInstructions) {
                session()->put('task_understood_' . $this->task->id, true);
            }

            $this->currentStep++;

            session()->flash('success', 'Melanjutkan ke ' . $this->getStepTitle());

            $this->dispatch('toast', [
                'message' => 'Melanjutkan ke ' . $this->getStepTitle(),
                'type' => 'success'
            ]);
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->dispatch('toast', [
                'message' => 'Kembali ke ' . $this->getStepTitle(),
                'type' => 'info'
            ]);
        }
    }

    private function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                $this->validate([
                    'understoodInstructions' => 'required|accepted'
                ], $this->messages);
                break;
            case 2:
                if ($this->canSubmitProof1()) {
                    $this->validateProof(1);
                }
                break;
            case 3:
                if ($this->canSubmitProof2()) {
                    $this->validateProof(2);
                }
                break;
        }
    }

    private function validateProof($stage)
    {
        $files = $stage === 1 ? $this->proof1Files : $this->proof2Files;
        $description = $stage === 1 ? $this->proof1Description : $this->proof2Description;
        $fileKey = $stage === 1 ? 'proof1Files' : 'proof2Files';
        $descKey = $stage === 1 ? 'proof1Description' : 'proof2Description';

        if (empty($files) && empty($description)) {
            $this->addError($fileKey, 'Harap upload bukti file ATAU isi deskripsi/link.');
            $this->addError($descKey, 'Harap upload bukti file ATAU isi deskripsi/link.');
            // Throw validation exception
            $this->validate([
                $fileKey => 'required_without:' . $descKey,
                $descKey => 'required_without:' . $fileKey,
            ]);
        }

        if (!empty($files)) {
            $this->validate([
                $fileKey . '.*' => 'file|max:10240', // 10MB
            ], [
                $fileKey . '.*.max' => 'Maksimal ukuran file adalah 10MB.',
            ]);
        }

        if (!empty($description)) {
            $this->validate([
                $descKey => 'min:5',
            ], [
                $descKey . '.min' => 'Deskripsi minimal 5 karakter.',
            ]);
        }
    }

    public function submitProof1()
    {
        // Security: Verify ownership and permissions
        if (!$this->verifyUserTaskOwnership()) {
            return;
        }

        $this->validateProof(1);

        try {
            // Upload files
            $uploadedFiles = [];
            foreach ($this->proof1Files as $file) {
                $path = $file->store('task-proofs/' . $this->task->id . '/verification-1', 'public');
                $uploadedFiles[] = $path;
            }

            // Update user task - pisahkan file paths dan status untuk avoid VARCHAR limit
            $this->userTask->update([
                'status' => UserTask::STATUS_PENDING_VERIFICATION_1,
                'verification_1_status' => 'Submitted at ' . now()->format('Y-m-d H:i:s') . '. Description: ' . $this->proof1Description,
                'verification_1_files' => $uploadedFiles, // Laravel Model Cast will auto-encode to JSON
            ]);

            session()->flash('success', 'Stage 1 proof submitted successfully! Waiting for admin verification.');

            $this->dispatch('toast', [
                'message' => 'Stage 1 proof submitted successfully! Waiting for admin verification.',
                'type' => 'success'
            ]);

            $this->setCurrentStepFromStatus();

            // Refresh component state
            $this->userTask->refresh();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error submitting proof: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function submitProof2()
    {
        // Security: Verify ownership and permissions
        if (!$this->verifyUserTaskOwnership()) {
            return;
        }

        $this->validateProof(2);

        try {
            // Upload files
            $uploadedFiles = [];
            foreach ($this->proof2Files as $file) {
                $path = $file->store('task-proofs/' . $this->task->id . '/verification-2', 'public');
                $uploadedFiles[] = $path;
            }

            // Update user task - pisahkan file paths dan status untuk avoid VARCHAR limit
            $this->userTask->update([
                'status' => UserTask::STATUS_PENDING_VERIFICATION_2,
                'verification_2_status' => 'Submitted at ' . now()->format('Y-m-d H:i:s') . '. Description: ' . $this->proof2Description,
                'verification_2_files' => $uploadedFiles, // Laravel Model Cast will auto-encode to JSON
            ]);

            // Reset form fields for proof 2
            $this->reset(['proof2Files', 'proof2Description']);

            session()->flash('success', 'Stage 2 proof submitted successfully! Awaiting final admin verification.');

            $this->dispatch('toast', [
                'message' => 'Stage 2 proof submitted successfully! Awaiting final admin verification.',
                'type' => 'success'
            ]);

            $this->setCurrentStepFromStatus();

            // Refresh component state
            $this->userTask->refresh();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error submitting proof: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function cancelTask()
    {
        try {
            // Security: Verify ownership and permissions
            if (!$this->verifyUserTaskOwnership()) {
                return;
            }

            // Check if cancellation is allowed
            if (!$this->canCancelTask()) {
                session()->flash('error', 'Tidak dapat membatalkan task setelah submit proof untuk verifikasi.');
                return;
            }

            // Validate user task exists
            if (!$this->userTask) {
                $this->dispatch('redirect-to-dashboard');
                return;
            }

            // Update current user task status and reset verification data
            $updated = $this->userTask->update([
                'status' => UserTask::STATUS_CANCELLED,
                'cancelled_at' => now(),
                // Reset semua verification status dan timestamps
                'verification_1_status' => null,
                'verification_2_status' => null,
                'verification_1_approved_at' => null,
                'verification_2_approved_at' => null,
                'verification_1_approved_by' => null,
                'verification_2_approved_by' => null,
                'completed_at' => null,
                'payment_amount' => null,
                'payment_status' => UserTask::PAYMENT_PENDING,
                'payment_verified_by_admin_id' => null,
                'payment_verified_at' => null,
            ]);

            // Make sure no other active user_tasks are holding this task
            // mark them as failed and reset their verification fields so task becomes available
            \App\Models\UserTask::where('task_id', $this->task->id)
                ->whereIn('status', [UserTask::STATUS_TAKEN, UserTask::STATUS_PENDING_VERIFICATION_1, UserTask::STATUS_PENDING_VERIFICATION_2])
                ->where('id', '!=', $this->userTask->id)
                ->update([
                    'status' => UserTask::STATUS_FAILED,
                    'failed_count' => DB::raw('COALESCE(failed_count, 0) + 1'),
                    'verification_1_status' => null,
                    'verification_2_status' => null,
                    'verification_1_approved_at' => null,
                    'verification_2_approved_at' => null,
                    'verification_1_approved_by' => null,
                    'verification_2_approved_by' => null,
                    'payment_status' => UserTask::PAYMENT_PENDING,
                    'payment_amount' => null,
                ]);

            if ($updated) {
                // Clear the understanding session for this task
                session()->forget('task_understood_' . $this->task->id);

                session()->flash('success', 'Task cancelled successfully and released to pool.');
                $this->dispatch('redirect-to-dashboard');
            } else {
                $this->dispatch('toast', [
                    'message' => 'Failed to cancel task.',
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error cancelling task: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            $this->dispatch('redirect-to-dashboard');
        }
    }

    public function getStepTitle()
    {
        return match ($this->currentStep) {
            1 => 'Instruksi Tugas',
            2 => 'Upload Bukti 1',
            3 => 'Upload Bukti 2',
            4 => 'Selesai',
            default => 'Tidak Diketahui'
        };
    }

    public function getStepLabel($step)
    {
        return match ($step) {
            1 => 'Instruksi',
            2 => 'Bukti 1',
            3 => 'Bukti 2',
            4 => 'Selesai',
            default => 'Langkah ' . $step
        };
    }

    public function canSubmitProof1()
    {
        // Jika task sudah dikembalikan ke pool, tidak bisa submit lagi
        if ($this->isTaskReturnedToPool()) {
            return false;
        }

        // Jika sudah submit proof1 dan sedang waiting verification, tidak bisa submit lagi
        if (
            $this->userTask->verification_1_status &&
            strpos($this->userTask->verification_1_status, 'Submitted') !== false &&
            strpos($this->userTask->verification_1_status, 'Approved') === false &&
            strpos($this->userTask->verification_1_status, 'Rejected') === false
        ) {
            return false;
        }

        return in_array($this->userTask->status, [UserTask::STATUS_TAKEN, UserTask::STATUS_FAILED]);
    }

    public function canSubmitProof2()
    {
        // Jika task sudah dikembalikan ke pool, tidak bisa submit lagi
        if ($this->isTaskReturnedToPool()) {
            return false;
        }

        // Jika sudah submit proof2 dan sedang waiting verification, tidak bisa submit lagi
        if (
            $this->userTask->verification_2_status &&
            strpos($this->userTask->verification_2_status, 'Submitted') !== false &&
            strpos($this->userTask->verification_2_status, 'Approved') === false &&
            strpos($this->userTask->verification_2_status, 'Rejected') === false
        ) {
            return false;
        }

        // Bisa submit proof2 jika:
        // 1. Status pending_verification_2 (sudah diapprove stage 1 oleh admin)
        // 2. Atau status failed tapi verification_2_status sudah ada (reject stage 2, bisa resubmit)
        return $this->userTask->status === UserTask::STATUS_PENDING_VERIFICATION_2 ||
            ($this->userTask->status === UserTask::STATUS_FAILED &&
                $this->userTask->verification_2_status &&
                strpos($this->userTask->verification_2_status, 'Rejected') !== false);
    }

    public function isWaitingVerification1()
    {
        // Jika task sudah dikembalikan ke pool, tidak waiting lagi
        if ($this->isTaskReturnedToPool()) {
            return false;
        }

        return $this->userTask->status === UserTask::STATUS_PENDING_VERIFICATION_1;
    }

    public function isWaitingVerification2()
    {
        // Jika task sudah dikembalikan ke pool, tidak waiting lagi
        if ($this->isTaskReturnedToPool()) {
            return false;
        }

        // Waiting verification 2 jika sudah submit proof2 tapi belum approved
        return $this->userTask->status === UserTask::STATUS_PENDING_VERIFICATION_2 &&
            $this->userTask->verification_2_status &&
            strpos($this->userTask->verification_2_status, 'Submitted') !== false;
    }

    public function isCompleted()
    {
        // Task is only truly completed if status is completed AND payment is marked as success
        return $this->userTask->status === UserTask::STATUS_COMPLETED &&
            $this->userTask->payment_status === 'success';
    }

    public function isCompletedButAwaitingPayment()
    {
        // Task work is completed but payment is still pending/processing
        return $this->userTask->status === UserTask::STATUS_COMPLETED &&
            in_array($this->userTask->payment_status, ['pending', 'failed']);
    }

    public function isFailed()
    {
        return $this->userTask->status === UserTask::STATUS_FAILED;
    }

    public function getVerificationMessage()
    {
        // Check for any rejection message regardless of current status
        if ($this->userTask->verification_2_status && strpos($this->userTask->verification_2_status, 'Rejected') !== false) {
            return $this->userTask->verification_2_status;
        } elseif ($this->userTask->verification_1_status && strpos($this->userTask->verification_1_status, 'Rejected') !== false) {
            return $this->userTask->verification_1_status;
        }

        // For failed status, return any available message
        if ($this->userTask->status === UserTask::STATUS_FAILED) {
            if ($this->userTask->verification_2_status) {
                return $this->userTask->verification_2_status;
            } elseif ($this->userTask->verification_1_status) {
                return $this->userTask->verification_1_status;
            }
        }

        return null;
    }

    public function isTaskReturnedToPool()
    {
        return $this->userTask->status === UserTask::STATUS_FAILED &&
            $this->userTask->cancelled_at !== null &&
            (
                ($this->userTask->verification_1_status &&
                    strpos($this->userTask->verification_1_status, 'Task returned to available pool') !== false) ||
                ($this->userTask->verification_2_status &&
                    strpos($this->userTask->verification_2_status, 'Task returned to available pool') !== false)
            );
    }

    public function isTaskExpired()
    {
        // Task failed due to deadline expiry (not rejected by admin)
        return $this->userTask->status === UserTask::STATUS_FAILED
            && !$this->isTaskRejectedAndCancelled()
            && !$this->isTaskReturnedToPool();
    }

    public function isTaskRejectedAndCancelled()
    {
        return $this->userTask->status === UserTask::STATUS_FAILED &&
            (
                ($this->userTask->verification_1_status &&
                    strpos($this->userTask->verification_1_status, 'Rejected by admin') !== false) ||
                ($this->userTask->verification_2_status &&
                    strpos($this->userTask->verification_2_status, 'Rejected by admin') !== false)
            );
    }

    public function isTaskCompletedByAdmin()
    {
        return $this->userTask->status === UserTask::STATUS_COMPLETED &&
            $this->userTask->completed_at !== null &&
            (
                ($this->userTask->verification_2_status &&
                    strpos($this->userTask->verification_2_status, 'Approved by admin') !== false) ||
                ($this->userTask->verification_1_status &&
                    strpos($this->userTask->verification_1_status, 'Approved by admin') !== false &&
                    $this->userTask->verification_2_status &&
                    strpos($this->userTask->verification_2_status, 'Approved by admin') !== false)
            );
    }

    public function getRejectionFeedback()
    {
        if ($this->userTask->verification_2_status && strpos($this->userTask->verification_2_status, 'Rejected by admin') !== false) {
            // Extract reason from verification_2_status
            preg_match('/Rejected by admin at .+?\. (.+)$/', $this->userTask->verification_2_status, $matches);
            return isset($matches[1]) ? $matches[1] : 'Tidak ada detail feedback.';
        } elseif ($this->userTask->verification_1_status && strpos($this->userTask->verification_1_status, 'Rejected by admin') !== false) {
            // Extract reason from verification_1_status
            preg_match('/Rejected by admin at .+?\. (.+)$/', $this->userTask->verification_1_status, $matches);
            return isset($matches[1]) ? $matches[1] : 'Tidak ada detail feedback.';
        }
        return 'Tidak ada feedback ditemukan.';
    }

    public function render()
    {
        // Add safety check  
        if (!$this->task || !$this->userTask) {
            return redirect()->route('user.dashboard');
        }

        return view('livewire.task-work-wizard');
    }

    public function refreshUserTask()
    {
        // Force refresh user task data from database
        $this->userTask = $this->userTask->fresh();
    }

    /**
     * Security: Verify that current user owns this user task
     * Prevents manipulation via Postman or modified requests
     */
    private function verifyUserTaskOwnership(): bool
    {
        $user = Auth::user();

        // Check if user is still authenticated
        if (!$user) {
            session()->flash('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            $this->dispatch('redirect-to-login');
            return false;
        }

        // Check if user is banned
        if ($user->isBanned()) {
            session()->flash('error', 'Akun Anda sedang dibanned.');
            $this->dispatch('redirect-to-dashboard');
            return false;
        }

        // Check if admin trying to submit (shouldn't happen but double check)
        if ($user->isAdmin()) {
            session()->flash('error', 'Admin tidak diperbolehkan mengerjakan task.');
            $this->dispatch('redirect-to-dashboard');
            return false;
        }

        // Verify user task exists and belongs to current user
        if (!$this->userTask || $this->userTask->user_id !== $user->id) {
            session()->flash('error', 'Task ini bukan milik Anda.');
            $this->dispatch('redirect-to-dashboard');
            return false;
        }

        // Refresh user task data to prevent stale state attacks
        $this->userTask->refresh();

        return true;
    }
}
