<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Category;
use App\Models\UserTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class TaskDashboard extends Component
{
    use WithPagination;

    public $selectedCategory = null;
    public $search = '';
    public $filter = 'available'; // available, my_tasks, completed, failed
    public $viewMode = 'categories'; // categories, tasks

    // Weekly warning modal properties
    public $showWeeklyWarningModal = false;
    public $pendingTaskId = null;
    public $lastTaskDate = null;
    public $daysSinceLastTask = null;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Security: Check if user is banned
        if ($user->isBanned()) {
            Auth::logout();
            session()->flash('error', 'Akun Anda sedang dibanned.');
            return redirect()->route('login');
        }

        // Security: Redirect admin to filament
        if ($user->isAdmin()) {
            return redirect('/admin');
        }

        // Check if user has an ongoing task that still needs immediate action
        // Only redirect if task is in "taken" status (not yet submitted proof 1)
        // Users can take other tasks while waiting for admin approval
        $ongoingTask = UserTask::where('user_id', Auth::id())
            ->where('status', UserTask::STATUS_TAKEN)
            ->where(function ($q) {
                $q->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
            })
            ->with('task')
            ->first();

        if ($ongoingTask) {
            // Redirect to task work wizard only if proof 1 not yet submitted
            return redirect()->route('user.task.work', ['task' => $ongoingTask->task_id]);
        }
    }
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->viewMode = 'tasks';
        $this->resetPage();
    }

    public function backToCategories()
    {
        $this->selectedCategory = null;
        $this->viewMode = 'categories';
        $this->search = '';
        $this->resetPage();
    }

    public function takeTask($taskId)
    {
        $user = Auth::user();

        // Check if user is allowed to take tasks (admin restriction)
        if (!$user->canTakeTask()) {
            session()->flash('error', $user->getCannotTakeTaskReason() ?? 'Anda tidak diperbolehkan mengambil task.');
            return;
        }

        // Check weekly limit - get last completed task
        $lastCompletedTask = UserTask::where('user_id', Auth::id())
            ->where('status', UserTask::STATUS_COMPLETED)
            ->orderBy('completed_at', 'desc')
            ->first();

        if ($lastCompletedTask && $lastCompletedTask->completed_at) {
            $daysSince = (int) abs(now()->diffInDays($lastCompletedTask->completed_at));

            // If less than 7 days, show warning modal
            if ($daysSince < 7) {
                $this->pendingTaskId = $taskId;
                $this->lastTaskDate = $lastCompletedTask->completed_at->format('d M Y, H:i');
                $this->daysSinceLastTask = $daysSince === 0
                    ? 'Hari ini'
                    : $daysSince . ' hari yang lalu (' . (7 - $daysSince) . ' hari lagi)';
                $this->showWeeklyWarningModal = true;
                return;
            }
        }

        // Proceed with normal task taking
        $this->proceedTakeTask($taskId);
    }

    public function confirmTakeTask()
    {
        if ($this->pendingTaskId) {
            $this->proceedTakeTask($this->pendingTaskId);
            $this->cancelTakeTask();
        }
    }

    public function cancelTakeTask()
    {
        $this->showWeeklyWarningModal = false;
        $this->pendingTaskId = null;
        $this->lastTaskDate = null;
        $this->daysSinceLastTask = null;
    }

    private function proceedTakeTask($taskId)
    {
        $user = Auth::user();

        // Check if user already has an active task in "taken" status
        // Users can take new tasks while waiting for admin approval on submitted proofs
        $hasActiveTakenTask = UserTask::where('user_id', Auth::id())
            ->where('status', UserTask::STATUS_TAKEN)
            ->exists();

        if ($hasActiveTakenTask) {
            session()->flash('error', 'Anda masih memiliki task yang sedang dikerjakan. Selesaikan task tersebut terlebih dahulu.');
            return;
        }

        $task = Task::findOrFail($taskId);

        // Check if task is expired
        if ($task->isExpired()) {
            session()->flash('error', 'Task sudah tidak tersedia (expired)');
            return;
        }

        // Check if task's category is expired
        if ($task->category && $task->category->isExpired()) {
            session()->flash('error', 'Kategori task sudah tidak tersedia (expired)');
            return;
        }

        // Check if task is available (not taken by anyone else)
        if ($task->isTaken()) {
            session()->flash('error', 'Task sudah diambil oleh user lain');
            return;
        }

        // Check if user already has any relationship with this task
        $existingUserTask = UserTask::where('task_id', $taskId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingUserTask) {
            // If task exists but is cancelled/failed, allow retaking
            if (in_array($existingUserTask->status, [UserTask::STATUS_CANCELLED, UserTask::STATUS_FAILED])) {
                // Delete old proof files from storage before resetting
                $this->deleteOldProofFiles($existingUserTask);

                // Clear session understanding state for fresh start
                session()->forget('task_understood_' . $taskId);

                $existingUserTask->update([
                    'status' => UserTask::STATUS_TAKEN,
                    'taken_at' => now(),
                    'deadline_at' => now()->addDays(3),
                    'cancelled_at' => null,
                    'failed_count' => $existingUserTask->failed_count,
                    // Reset semua verification data untuk fresh start
                    'verification_1_status' => null,
                    'verification_2_status' => null,
                    'verification_1_files' => null, // Reset uploaded proof 1 files
                    'verification_2_files' => null, // Reset uploaded proof 2 files
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

                session()->flash('success', 'Task berhasil diambil kembali! Silakan kerjakan task Anda.');
                return redirect()->route('user.task.work', $taskId);
            } else {
                session()->flash('error', 'Anda sudah mengambil task ini');
                return;
            }
        }

        // Double-check that no one else has taken this task (race condition prevention)
        $activeTaskExists = UserTask::where('task_id', $taskId)
            ->whereIn('status', [
                UserTask::STATUS_TAKEN,
                UserTask::STATUS_PENDING_VERIFICATION_1,
                UserTask::STATUS_PENDING_VERIFICATION_2
            ])
            ->exists();

        if ($activeTaskExists) {
            session()->flash('error', 'Task sudah diambil oleh user lain');
            return;
        }

        // Create user task
        try {
            UserTask::create([
                'task_id' => $taskId,
                'user_id' => Auth::id(),
                'status' => UserTask::STATUS_TAKEN,
                'taken_at' => now(),
                'deadline_at' => now()->addDays(3), // Default 3 days deadline
            ]);

            session()->flash('success', 'Task berhasil diambil! Silakan kerjakan task Anda.');
            return redirect()->route('user.task.work', $taskId);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique constraint violation
            if ($e->getCode() === '23000') {
                session()->flash('error', 'Task sudah diambil oleh user lain');
                return;
            }
            throw $e;
        }
    }

    public function getTasks()
    {
        $query = Task::with(['category', 'creator', 'activeUserTask.user', 'userTasks' => function ($q) {
            $q->where('user_id', Auth::id());
        }])
            ->active();

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        switch ($this->filter) {
            case 'available':
                $query->available()
                    ->whereDoesntHave('userTasks', function ($q) {
                        $q->whereIn('status', [
                            UserTask::STATUS_TAKEN,
                            UserTask::STATUS_PENDING_VERIFICATION_1,
                            UserTask::STATUS_PENDING_VERIFICATION_2,
                            UserTask::STATUS_COMPLETED
                        ]);
                    });
                break;
            case 'my_tasks':
                $query->whereHas('userTasks', function ($q) {
                    $q->where('user_id', Auth::id())
                        ->whereIn('status', [
                            UserTask::STATUS_TAKEN,
                            UserTask::STATUS_PENDING_VERIFICATION_1,
                            UserTask::STATUS_PENDING_VERIFICATION_2
                        ]);
                });
                break;
            case 'completed':
                $query->whereHas('userTasks', function ($q) {
                    $q->where('user_id', Auth::id())
                        ->where('status', UserTask::STATUS_COMPLETED);
                });
                break;
            case 'failed':
                $query->whereHas('userTasks', function ($q) {
                    $q->where('user_id', Auth::id())
                        ->where(function ($q2) {
                            $q2->whereIn('status', [UserTask::STATUS_FAILED, UserTask::STATUS_CANCELLED])
                                ->orWhere(function ($q3) {
                                    $q3->whereIn('status', [UserTask::STATUS_TAKEN, UserTask::STATUS_PENDING_VERIFICATION_1, UserTask::STATUS_PENDING_VERIFICATION_2])
                                        ->whereNotNull('deadline_at')
                                        ->where('deadline_at', '<=', now());
                                });
                        });
                });
                break;
        }

        return $query->orderBy('priority_order')->paginate(12);
    }

    public function getCategories()
    {
        return Category::select('categories.*')
            ->where('is_active', true) // Only active categories
            ->where('expired_at', '>', now()) // Only non-expired categories
            ->withCount(['tasks as available_tasks_count' => function ($query) {
                $query->available()
                    ->where('expired_at', '>', now()) // Only non-expired tasks
                    ->whereDoesntHave('userTasks', function ($q) {
                        $q->whereIn('status', [
                            UserTask::STATUS_TAKEN,
                            UserTask::STATUS_PENDING_VERIFICATION_1,
                            UserTask::STATUS_PENDING_VERIFICATION_2,
                            UserTask::STATUS_COMPLETED
                        ]);
                    });
            }])
            ->withCount(['tasks as in_progress_count' => function ($query) {
                $query->whereHas('userTasks', function ($q) {
                    $q->where('user_id', Auth::id())
                        ->whereIn('status', [
                            UserTask::STATUS_TAKEN,
                            UserTask::STATUS_PENDING_VERIFICATION_1,
                            UserTask::STATUS_PENDING_VERIFICATION_2
                        ]);
                });
            }])
            ->whereHas('tasks', function ($query) {
                $query->available()
                    ->where('expired_at', '>', now()) // Only non-expired tasks
                    ->whereDoesntHave('userTasks', function ($q) {
                        $q->whereIn('status', [
                            UserTask::STATUS_TAKEN,
                            UserTask::STATUS_PENDING_VERIFICATION_1,
                            UserTask::STATUS_PENDING_VERIFICATION_2,
                            UserTask::STATUS_COMPLETED
                        ]);
                    });
            })
            // Order: Premium admin categories first, then by name
            ->with('createdBy') // Eager load creator
            ->orderByRaw("
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM users 
                        WHERE users.id = categories.created_by 
                        AND users.badge = 'premium_admin'
                    ) THEN 0 
                    ELSE 1 
                END
            ")
            ->orderBy('name')
            ->get();
    }

    /**
     * Delete old proof files from storage when user retakes a failed/cancelled task
     */
    private function deleteOldProofFiles(UserTask $userTask): void
    {
        // Delete verification 1 files
        if (!empty($userTask->verification_1_files)) {
            foreach ($userTask->verification_1_files as $file) {
                if (isset($file['path']) && Storage::disk('public')->exists($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }

        // Delete verification 2 files
        if (!empty($userTask->verification_2_files)) {
            foreach ($userTask->verification_2_files as $file) {
                if (isset($file['path']) && Storage::disk('public')->exists($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }
    }

    public function render()
    {
        $tasks = $this->viewMode === 'tasks' ? $this->getTasks() : collect();
        $categories = $this->getCategories();
        $selectedCategoryName = $this->selectedCategory ? Category::find($this->selectedCategory)?->name : null;
        $selectedCategoryAdmin = $this->selectedCategory ? Category::with('createdBy')->find($this->selectedCategory)?->createdBy : null;

        return view('livewire.task-dashboard', [
            'tasks' => $tasks,
            'categories' => $categories,
            'selectedCategoryName' => $selectedCategoryName,
            'selectedCategoryAdmin' => $selectedCategoryAdmin,
            'showWeeklyWarningModal' => $this->showWeeklyWarningModal,
            'lastTaskDate' => $this->lastTaskDate,
            'daysSinceLastTask' => $this->daysSinceLastTask,
        ]);
    }
}
