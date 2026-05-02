<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\UserTask;
use Filament\Widgets\ChartWidget;

class TasksStatusChart extends ChartWidget
{
    protected ?string $heading = 'Status Pengerjaan Tasks';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $user = auth()->user();

        // Get task IDs based on role
        if ($user->isSuperAdmin()) {
            $taskIds = Task::pluck('id');
        } else {
            $taskIds = Task::where('created_by', $user->id)->pluck('id');
        }

        $taken = UserTask::whereIn('task_id', $taskIds)
            ->where('status', 'taken')->count();
        $pendingVerification = UserTask::whereIn('task_id', $taskIds)
            ->whereIn('status', ['pending_verification_1', 'pending_verification_2'])->count();
        $completed = UserTask::whereIn('task_id', $taskIds)
            ->where('status', 'completed')->count();
        $failed = UserTask::whereIn('task_id', $taskIds)
            ->where('status', 'failed')->count();
        $cancelled = UserTask::whereIn('task_id', $taskIds)
            ->where('status', 'cancelled')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Tasks',
                    'data' => [$taken, $pendingVerification, $completed, $failed, $cancelled],
                    'backgroundColor' => [
                        '#3B82F6', // blue - taken
                        '#F59E0B', // yellow - pending verification
                        '#10B981', // green - completed
                        '#EF4444', // red - failed
                        '#6B7280', // gray - cancelled
                    ],
                ],
            ],
            'labels' => ['Diambil', 'Verifikasi', 'Selesai', 'Gagal', 'Batal'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
