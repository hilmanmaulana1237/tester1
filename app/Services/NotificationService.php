<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserTask;

class NotificationService
{
    /**
     * Create a generic notification.
     */
    public static function create(
        int $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?string $actionUrl = null
    ): Notification {
        $notificationType = match ($type) {
            'success' => Notification::TYPE_TASK_APPROVED,
            'error' => Notification::TYPE_TASK_REJECTED,
            'warning' => Notification::TYPE_DEADLINE_WARNING,
            default => Notification::TYPE_ADMIN_MESSAGE,
        };

        return Notification::create([
            'user_id' => $userId,
            'type' => $notificationType,
            'title' => $title,
            'message' => $message,
            'data' => array_filter([
                'action_url' => $actionUrl,
            ]),
        ]);
    }

    /**
     * Notify about new available tasks.
     */
    public static function notifyNewTasks(int $userId, int $count): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => Notification::TYPE_NEW_TASKS,
            'title' => 'Tugas Baru Tersedia!',
            'message' => "{$count} tugas baru telah ditambahkan. Buruan ambil sebelum kehabisan!",
            'data' => [
                'count' => $count,
                'action_url' => '/user/dashboard',
            ],
        ]);
    }

    /**
     * Notify all active users about new tasks.
     */
    public static function notifyAllUsersNewTasks(int $count): void
    {
        $activeUsers = User::where('is_banned', false)
            ->where('role', 'user')
            ->get();

        foreach ($activeUsers as $user) {
            self::notifyNewTasks($user->id, $count);
        }
    }

    /**
     * Notify user about task approval (V1 approved → user can submit V2).
     */
    public static function notifyTaskApproved(UserTask $userTask): Notification
    {
        $payment = $userTask->payment_amount ? 'Rp ' . number_format((float) $userTask->payment_amount, 0, ',', '.') : '';

        return Notification::create([
            'user_id' => $userTask->user_id,
            'type' => Notification::TYPE_TASK_APPROVED,
            'title' => 'Tugas Disetujui! ✅',
            'message' => "Selamat! Tugas '{$userTask->task->title}' telah disetujui. {$payment}",
            'data' => [
                'task_id' => $userTask->task_id,
                'user_task_id' => $userTask->id,
                'amount' => $userTask->payment_amount,
                'action_url' => '/user/task/' . $userTask->task_id . '/work',
            ],
        ]);
    }

    /**
     * Notify user about task rejection.
     */
    public static function notifyTaskRejected(UserTask $userTask, ?string $reason = null): Notification
    {
        $message = "Maaf, tugas '{$userTask->task->title}' tidak disetujui.";
        if ($reason) {
            $message .= " Alasan: {$reason}";
        }

        return Notification::create([
            'user_id' => $userTask->user_id,
            'type' => Notification::TYPE_TASK_REJECTED,
            'title' => 'Tugas Ditolak',
            'message' => $message,
            'data' => [
                'task_id' => $userTask->task_id,
                'user_task_id' => $userTask->id,
                'reason' => $reason,
                'action_url' => '/user/my-tasks',
            ],
        ]);
    }

    /**
     * Notify user about payment success.
     */
    public static function notifyPaymentSuccess(UserTask $userTask): Notification
    {
        $amount = 'Rp ' . number_format((float) ($userTask->payment_amount ?? 0), 0, ',', '.');

        return Notification::create([
            'user_id' => $userTask->user_id,
            'type' => Notification::TYPE_PAYMENT_SUCCESS,
            'title' => 'Pembayaran Berhasil! 💰',
            'message' => "Pembayaran {$amount} untuk tugas '{$userTask->task->title}' telah masuk ke akun Anda!",
            'data' => [
                'task_id' => $userTask->task_id,
                'user_task_id' => $userTask->id,
                'amount' => $userTask->payment_amount,
                'action_url' => '/user/my-tasks',
            ],
        ]);
    }

    /**
     * Notify user about approaching deadline.
     */
    public static function notifyDeadlineWarning(UserTask $userTask): Notification
    {
        $hours = now()->diffInHours($userTask->deadline_at);

        return Notification::create([
            'user_id' => $userTask->user_id,
            'type' => Notification::TYPE_DEADLINE_WARNING,
            'title' => 'Deadline Mendekat! ⏰',
            'message' => "Tugas '{$userTask->task->title}' akan berakhir dalam {$hours} jam!",
            'data' => [
                'task_id' => $userTask->task_id,
                'user_task_id' => $userTask->id,
                'deadline' => $userTask->deadline_at,
                'action_url' => '/user/task/' . $userTask->task_id . '/work',
            ],
        ]);
    }

    /**
     * Notify user about badge level up.
     */
    public static function notifyBadgeLevelUp(User $user, string $newBadge): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_BADGE_LEVELUP,
            'title' => 'Level Badge Naik! 🏆',
            'message' => "Selamat! Kamu naik ke badge {$newBadge}! Terus semangat!",
            'data' => [
                'badge' => $newBadge,
                'action_url' => '/dashboard',
            ],
        ]);
    }

    /**
     * Notify user about task expiration.
     */
    public static function notifyTaskExpired(UserTask $userTask): Notification
    {
        return Notification::create([
            'user_id' => $userTask->user_id,
            'type' => Notification::TYPE_TASK_EXPIRED,
            'title' => 'Tugas Kadaluarsa',
            'message' => "Waktu untuk tugas '{$userTask->task->title}' telah habis.",
            'data' => [
                'task_id' => $userTask->task_id,
                'user_task_id' => $userTask->id,
                'action_url' => '/user/my-tasks',
            ],
        ]);
    }

    /**
     * Notify user about new chat message from admin.
     * Dijalankan ketika admin mengirim pesan via SupportChat atau TaskChat.
     */
    public static function notifyChatMessage(int $userId, int $taskId, string $adminName, string $preview): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => Notification::TYPE_ADMIN_MESSAGE,
            'title' => "Pesan dari {$adminName} 💬",
            'message' => \Illuminate\Support\Str::limit($preview, 80),
            'data' => [
                'task_id' => $taskId,
                'action_url' => '/user/task/' . $taskId . '/work',
            ],
        ]);
    }

    /**
     * Send admin broadcast message to all users.
     */
    public static function notifyAdminMessage(string $title, string $message, ?array $userIds = null): int
    {
        $query = User::where('is_banned', false)->where('role', 'user');

        if ($userIds) {
            $query->whereIn('id', $userIds);
        }

        $users = $query->get();
        $count = 0;

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => Notification::TYPE_ADMIN_MESSAGE,
                'title' => $title,
                'message' => $message,
                'data' => [
                    'action_url' => '/dashboard',
                ],
            ]);
            $count++;
        }

        return $count;
    }
}
