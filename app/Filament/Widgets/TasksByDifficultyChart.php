<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TasksByDifficultyChart extends ChartWidget
{
    protected ?string $heading = 'Tasks berdasarkan Tingkat Kesulitan';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $user = auth()->user();

        $query = Task::query();

        // Filter based on role
        if (!$user->isSuperAdmin()) {
            $query->where('created_by', $user->id);
        }

        $easy = (clone $query)->where('difficulty_level', Task::DIFFICULTY_EASY)->count();
        $medium = (clone $query)->where('difficulty_level', Task::DIFFICULTY_MEDIUM)->count();
        $hard = (clone $query)->where('difficulty_level', Task::DIFFICULTY_HARD)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Tasks',
                    'data' => [$easy, $medium, $hard],
                    'backgroundColor' => [
                        '#10B981', // green - easy
                        '#F59E0B', // yellow - medium
                        '#EF4444', // red - hard
                    ],
                ],
            ],
            'labels' => ['Mudah', 'Sedang', 'Sulit'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
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
