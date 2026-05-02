<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminStatsOverview;
use App\Filament\Widgets\LatestUserTasksWidget;
use App\Filament\Widgets\SuperadminStatsOverview;
use App\Filament\Widgets\TasksByDifficultyChart;
use App\Filament\Widgets\TasksStatusChart;
use App\Filament\Widgets\RecentActivitiesWidget;
use App\Filament\Widgets\TopPerformersWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    public function getWidgets(): array
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            // Superadmin melihat semua statistik
            return [
                SuperadminStatsOverview::class,
                TasksStatusChart::class,
                TasksByDifficultyChart::class,
                LatestUserTasksWidget::class,
                TopPerformersWidget::class,
                RecentActivitiesWidget::class,
            ];
        }

        // Admin biasa hanya melihat statistik miliknya
        return [
            AdminStatsOverview::class,
            TasksStatusChart::class,
            TasksByDifficultyChart::class,
            LatestUserTasksWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
