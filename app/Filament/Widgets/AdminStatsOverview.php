<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Task;
use App\Models\UserTask;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $adminId = auth()->id();

        // My Tasks
        $myTasks = Task::where('created_by', $adminId)->count();
        $myActiveTasks = Task::where('created_by', $adminId)
            ->where('is_expired', false)
            ->where(function ($q) {
                $q->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            })->count();

        // My Categories
        $myCategories = Category::where('created_by', $adminId)->count();
        $myActiveCategories = Category::where('created_by', $adminId)
            ->where('is_active', true)->count();

        // UserTasks for my tasks
        $myTaskIds = Task::where('created_by', $adminId)->pluck('id');

        $totalUserTasks = UserTask::whereIn('task_id', $myTaskIds)->count();
        $completedTasks = UserTask::whereIn('task_id', $myTaskIds)
            ->where('status', 'completed')->count();
        $pendingVerification = UserTask::whereIn('task_id', $myTaskIds)
            ->whereIn('status', ['pending_verification_1', 'pending_verification_2'])->count();
        $takenTasks = UserTask::whereIn('task_id', $myTaskIds)
            ->where('status', 'taken')->count();
        $failedTasks = UserTask::whereIn('task_id', $myTaskIds)
            ->where('status', 'failed')->count();

        // My earnings/payouts
        $totalPaidOut = UserTask::whereIn('task_id', $myTaskIds)
            ->where('status', 'completed')
            ->where('payment_status', 'success')
            ->sum('payment_amount');

        $pendingPayment = UserTask::whereIn('task_id', $myTaskIds)
            ->where('status', 'completed')
            ->where('payment_status', 'pending')
            ->sum('payment_amount');

        // Completion rate
        $completionRate = $totalUserTasks > 0
            ? round(($completedTasks / $totalUserTasks) * 100, 1)
            : 0;

        return [
            Stat::make('Tasks Saya', $myTasks)
                ->description("{$myActiveTasks} aktif")
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary')
                ->chart([3, 5, 7, 4, 6, 8, $myTasks]),

            Stat::make('Categories Saya', $myCategories)
                ->description("{$myActiveCategories} aktif")
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),

            Stat::make('Total Pengerjaan', $totalUserTasks)
                ->description('User mengerjakan task saya')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Menunggu Verifikasi', $pendingVerification)
                ->description('Perlu review segera')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingVerification > 0 ? 'warning' : 'gray'),

            Stat::make('Sedang Dikerjakan', $takenTasks)
                ->description('Tasks diambil user')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('Selesai', $completedTasks)
                ->description("{$completionRate}% completion rate")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Gagal/Ditolak', $failedTasks)
                ->description('Tasks failed')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($failedTasks > 0 ? 'danger' : 'gray'),

            Stat::make('Total Dibayar', 'Rp ' . number_format($totalPaidOut, 0, ',', '.'))
                ->description('Dari task saya')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pending Payment', 'Rp ' . number_format($pendingPayment, 0, ',', '.'))
                ->description('Menunggu pembayaran')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color($pendingPayment > 0 ? 'danger' : 'gray'),
        ];
    }
}
