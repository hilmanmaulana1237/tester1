<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTask;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuperadminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Total Users
        $totalUsers = User::where('role', 'user')->count();
        $newUsersThisWeek = User::where('role', 'user')
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        // Total Admins
        $totalAdmins = User::whereIn('role', ['admin', 'superadmin'])->count();

        // Total Tasks
        $totalTasks = Task::count();
        $activeTasks = Task::where('is_expired', false)
            ->where(function ($q) {
                $q->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            })->count();

        // Total Categories
        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();

        // UserTasks statistics
        $totalUserTasks = UserTask::count();
        $completedTasks = UserTask::where('status', 'completed')->count();
        $pendingVerification = UserTask::whereIn('status', ['pending_verification_1', 'pending_verification_2'])->count();
        $takenTasks = UserTask::where('status', 'taken')->count();

        // Revenue calculations
        $totalPaidOut = UserTask::where('status', 'completed')
            ->where('payment_status', 'success')
            ->sum('payment_amount');

        $pendingPayment = UserTask::where('status', 'completed')
            ->where('payment_status', 'pending')
            ->sum('payment_amount');

        // Task completion rate
        $completionRate = $totalUserTasks > 0
            ? round(($completedTasks / $totalUserTasks) * 100, 1)
            : 0;

        return [
            Stat::make('Total Users', $totalUsers)
                ->description("+{$newUsersThisWeek} minggu ini")
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, $newUsersThisWeek]),

            Stat::make('Total Admin', $totalAdmins)
                ->description('Superadmin & Admin')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning'),

            Stat::make('Total Tasks', $totalTasks)
                ->description("{$activeTasks} aktif")
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('success'),

            Stat::make('Total Categories', $totalCategories)
                ->description("{$activeCategories} aktif")
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),

            Stat::make('Tasks Selesai', $completedTasks)
                ->description("{$completionRate}% completion rate")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([2, 4, 6, 8, 5, 7, $completedTasks]),

            Stat::make('Menunggu Verifikasi', $pendingVerification)
                ->description('Perlu review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Sedang Dikerjakan', $takenTasks)
                ->description('Tasks diambil user')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('Total Dibayarkan', 'Rp ' . number_format($totalPaidOut, 0, ',', '.'))
                ->description('Pembayaran selesai')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Menunggu Pembayaran', 'Rp ' . number_format($pendingPayment, 0, ',', '.'))
                ->description('Belum dibayar')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('danger'),
        ];
    }
}
