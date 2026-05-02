<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTask;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class RecentActivitiesWidget extends Widget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 1;

    protected string $view = 'filament.widgets.recent-activities-widget';

    public function getActivities(): array
    {
        $activities = collect();

        // Recent user registrations
        $newUsers = User::where('role', 'user')
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($user) => [
                'type' => 'user_registered',
                'icon' => 'heroicon-o-user-plus',
                'color' => 'primary',
                'message' => "User baru: {$user->name}",
                'time' => $user->created_at,
            ]);

        // Recent task completions
        $completedTasks = UserTask::with(['user', 'task'])
            ->where('status', 'completed')
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->map(fn($ut) => [
                'type' => 'task_completed',
                'icon' => 'heroicon-o-check-circle',
                'color' => 'success',
                'message' => "{$ut->user->name} menyelesaikan \"{$ut->task->title}\"",
                'time' => $ut->updated_at,
            ]);

        // Recent task submissions (pending verification)
        $pendingTasks = UserTask::with(['user', 'task'])
            ->where('status', 'pending_verification')
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->map(fn($ut) => [
                'type' => 'task_submitted',
                'icon' => 'heroicon-o-clock',
                'color' => 'warning',
                'message' => "{$ut->user->name} submit \"{$ut->task->title}\"",
                'time' => $ut->updated_at,
            ]);

        // New tasks created
        $newTasks = Task::with('createdBy')
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($task) => [
                'type' => 'task_created',
                'icon' => 'heroicon-o-plus-circle',
                'color' => 'info',
                'message' => "Task baru: \"{$task->title}\"",
                'time' => $task->created_at,
            ]);

        // New categories
        $newCategories = Category::with('createdBy')
            ->latest()
            ->take(2)
            ->get()
            ->map(fn($cat) => [
                'type' => 'category_created',
                'icon' => 'heroicon-o-folder-plus',
                'color' => 'info',
                'message' => "Category baru: \"{$cat->name}\"",
                'time' => $cat->created_at,
            ]);

        return $activities
            ->merge($newUsers)
            ->merge($completedTasks)
            ->merge($pendingTasks)
            ->merge($newTasks)
            ->merge($newCategories)
            ->sortByDesc('time')
            ->take(10)
            ->values()
            ->toArray();
    }
}
