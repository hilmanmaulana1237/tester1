<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Default cache duration in minutes
     */
    protected static int $defaultDuration = 5;

    /**
     * Remember a value in cache
     *
     * @param string $key
     * @param callable $callback
     * @param int|null $minutes
     * @return mixed
     */
    public static function remember(string $key, callable $callback, ?int $minutes = null): mixed
    {
        $duration = $minutes ?? static::$defaultDuration;

        return Cache::remember($key, now()->addMinutes($duration), $callback);
    }

    /**
     * Remember a value in cache forever
     *
     * @param string $key
     * @param callable $callback
     * @return mixed
     */
    public static function rememberForever(string $key, callable $callback): mixed
    {
        return Cache::rememberForever($key, $callback);
    }

    /**
     * Generate a user-specific cache key
     *
     * @param int|string $userId
     * @param string $key
     * @return string
     */
    public static function userKey(int|string $userId, string $key): string
    {
        return "user_{$userId}_{$key}";
    }

    /**
     * Generate a task-specific cache key
     *
     * @param int|string $taskId
     * @return string
     */
    public static function taskKey(int|string $taskId): string
    {
        return "task_{$taskId}";
    }

    /**
     * Clear all task-related cache
     *
     * @return void
     */
    public static function clearTaskCache(): void
    {
        static::forget('available_tasks_count');
        static::forget('total_tasks_count');
        static::forget('active_tasks_count');
    }

    /**
     * Forget a cached value
     *
     * @param string $key
     * @return bool
     */
    public static function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Forget all user-specific cache
     *
     * @param int|string $userId
     * @param array $keys
     * @return void
     */
    public static function forgetUserCache(int|string $userId, array $keys = []): void
    {
        $defaultKeys = [
            'my_active_tasks_count',
            'completed_tasks_count',
            'pending_earnings',
            'total_earnings',
        ];

        $keysToForget = empty($keys) ? $defaultKeys : $keys;

        foreach ($keysToForget as $key) {
            static::forget(static::userKey($userId, $key));
        }
    }

    /**
     * Flush all cache
     *
     * @return bool
     */
    public static function flush(): bool
    {
        return Cache::flush();
    }

    /**
     * Check if a key exists in cache
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return Cache::has($key);
    }

    /**
     * Get a value from cache
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($key, $default);
    }

    /**
     * Put a value in cache
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $minutes
     * @return bool
     */
    public static function put(string $key, mixed $value, ?int $minutes = null): bool
    {
        $duration = $minutes ?? static::$defaultDuration;

        return Cache::put($key, $value, now()->addMinutes($duration));
    }
}
