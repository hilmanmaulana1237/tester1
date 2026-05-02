<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\UserTask;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Get available tasks with caching
     */
    public function getAvailableTasks()
    {
        // Cache selama 15 menit dengan tag 'tasks'
        $tasks = CacheService::remember(
            'available_tasks',
            function () {
                return Task::where('status', 'published')
                    ->whereDoesntHave('userTasks', function ($query) {
                        $query->active();
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            },
            CacheService::SHORT_TTL, // 15 menit
            [CacheService::TAG_TASKS]
        );

        return response()->json($tasks);
    }

    /**
     * Get user's active tasks with caching
     */
    public function getUserActiveTasks()
    {
        $userId = Auth::id();

        // Cache per user selama 5 menit
        $userTasks = CacheService::remember(
            CacheService::userKey($userId, 'active_tasks'),
            function () use ($userId) {
                return UserTask::with('task')
                    ->where('user_id', $userId)
                    ->active()
                    ->get();
            },
            5, // 5 menit (data sering berubah)
            [CacheService::TAG_TASKS, CacheService::TAG_USERS]
        );

        return response()->json($userTasks);
    }

    /**
     * Get user's completed tasks with caching
     */
    public function getUserCompletedTasks()
    {
        $userId = Auth::id();

        // Cache lebih lama karena data completed jarang berubah
        $completedTasks = CacheService::remember(
            CacheService::userKey($userId, 'completed_tasks'),
            function () use ($userId) {
                return UserTask::with('task')
                    ->where('user_id', $userId)
                    ->completed()
                    ->orderBy('completed_at', 'desc')
                    ->get();
            },
            CacheService::DEFAULT_TTL, // 60 menit
            [CacheService::TAG_TASKS, CacheService::TAG_USERS]
        );

        return response()->json($completedTasks);
    }

    /**
     * Get single task with caching
     */
    public function getTask($taskId)
    {
        $task = CacheService::remember(
            CacheService::taskKey($taskId),
            function () use ($taskId) {
                return Task::with(['category', 'userTasks'])
                    ->findOrFail($taskId);
            },
            CacheService::DEFAULT_TTL,
            [CacheService::TAG_TASKS]
        );

        return response()->json($task);
    }

    /**
     * Clear cache manually (untuk admin atau debugging)
     */
    public function clearCache(Request $request)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $type = $request->input('type', 'all');

        switch ($type) {
            case 'tasks':
                CacheService::clearTaskCache();
                break;
            case 'users':
                CacheService::clearUserCache();
                break;
            case 'categories':
                CacheService::clearCategoryCache();
                break;
            default:
                CacheService::clearTaskCache();
                CacheService::clearUserCache();
                CacheService::clearCategoryCache();
        }

        return response()->json(['message' => 'Cache cleared successfully']);
    }
}
