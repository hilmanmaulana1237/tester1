<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;
use App\Models\UserTask;

class FreshUserTasksSeeder extends Seeder
{
    /**
     * Hapus semua user task lama, lalu buat ulang data demo yang realistis.
     */
    public function run(): void
    {
        UserTask::truncate();

        $user = User::where('email', 'test@gmail.com')->first();

        if (!$user) {
            $this->command->error('User test@gmail.com tidak ditemukan. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $admin = User::where('role', 'admin')->first();
        $tasks = Task::limit(10)->get();

        if ($tasks->isEmpty()) {
            $this->command->error('Tidak ada task yang ditemukan. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        // --- 3 task selesai (completed + payment success) ---
        foreach ($tasks->take(3)->values() as $index => $task) {
            $takenAt          = now()->subHours(rand(24, 72));
            $proof1At         = $takenAt->copy()->addMinutes(7);
            $proof1ApprovedAt = $proof1At->copy()->addMinutes(20);
            $proof2At         = $proof1ApprovedAt->copy()->addMinutes(40);
            $completedAt      = $proof2At->copy()->addMinutes(10);
            $amounts          = [15000, 25000, 20000];

            UserTask::create([
                'task_id'                    => $task->id,
                'user_id'                    => $user->id,
                'status'                     => UserTask::STATUS_COMPLETED,
                'taken_at'                   => $takenAt,
                'deadline_at'                => $takenAt->copy()->addHours(24),
                'completed_at'               => $completedAt,
                'verification_1_status'      => 'Submitted at ' . $proof1At->format('Y-m-d H:i:s') . '. Description: Sudah merekrut ' . rand(20, 28) . ' anggota baru. Screenshot terlampir. - Approved by admin at ' . $proof1ApprovedAt->format('Y-m-d H:i:s'),
                'verification_1_files'       => ['task-proofs/' . $task->id . '/verification-1/bukti_tahap1.jpg'],
                'verification_1_approved_at' => $proof1ApprovedAt,
                'verification_1_approved_by' => $admin?->id,
                'verification_2_status'      => 'Submitted at ' . $proof2At->format('Y-m-d H:i:s') . '. Description: Semua anggota masih aktif di grup setelah 48 jam. - Approved by admin at ' . $completedAt->format('Y-m-d H:i:s'),
                'verification_2_files'       => ['task-proofs/' . $task->id . '/verification-2/bukti_tahap2.jpg'],
                'verification_2_approved_at' => $completedAt,
                'verification_2_approved_by' => $admin?->id,
                'payment_status'             => UserTask::PAYMENT_SUCCESS,
                'payment_amount'             => $amounts[$index],
                'payment_verified_by_admin_id' => $admin?->id,
                'payment_verified_at'          => $completedAt->copy()->addMinutes(5),
                'failed_count'               => 0,
            ]);
        }

        $this->command->info('✔ Dibuat 3 task selesai (completed)');

        // --- 2 task pending verification 1 ---
        foreach ($tasks->skip(3)->take(2) as $task) {
            $takenAt     = now()->subMinutes(rand(20, 60));
            $submittedAt = $takenAt->copy()->addMinutes(8);

            UserTask::create([
                'task_id'               => $task->id,
                'user_id'               => $user->id,
                'status'                => UserTask::STATUS_PENDING_VERIFICATION_1,
                'taken_at'              => $takenAt,
                'deadline_at'           => $takenAt->copy()->addHours(24),
                'verification_1_status' => 'Submitted at ' . $submittedAt->format('Y-m-d H:i:s') . '. Description: Target rekrutmen tercapai. Daftar anggota dan screenshot konfirmasi join sudah terlampir.',
                'verification_1_files'  => ['task-proofs/' . $task->id . '/verification-1/bukti_submit.jpg'],
                'payment_status'        => UserTask::PAYMENT_PENDING,
                'failed_count'          => 0,
            ]);
        }

        $this->command->info('✔ Dibuat 2 task menunggu verifikasi tahap 1');

        // --- 1 task gagal (kadaluarsa) ---
        $gagalTask = $tasks->skip(5)->first();
        UserTask::create([
            'task_id'               => $gagalTask->id,
            'user_id'               => $user->id,
            'status'                => UserTask::STATUS_FAILED,
            'taken_at'              => now()->subHours(4),
            'deadline_at'           => now()->subHours(3),
            'cancelled_at'          => now()->subHours(3),
            'verification_1_status' => 'Failed: Tidak mengirimkan bukti dalam 10 menit setelah mengambil task. Task otomatis dibatalkan pada ' . now()->subHours(3)->format('Y-m-d H:i:s') . '.',
            'payment_status'        => UserTask::PAYMENT_PENDING,
            'failed_count'          => 1,
        ]);

        $this->command->info('✔ Dibuat 1 task gagal/kadaluarsa');
        $this->command->info('');
        $this->command->info('Total: 6 user task berhasil dibuat untuk ' . $user->name . ' (' . $user->email . ')');
    }
}
