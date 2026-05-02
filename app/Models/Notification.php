<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Notification extends Model
{
    // Auto-invalidate per-user notification cache on any mutation
    protected static function booted(): void
    {
        $bust = function (self $notification) {
            $uid = $notification->user_id;
            Cache::forget("notif_panel.{$uid}.all");
            Cache::forget("notif_panel.{$uid}.unread");
            Cache::forget("notif_panel.{$uid}.count");
        };

        static::created($bust);
        static::updated($bust);
        static::deleted($bust);
    }

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Notification types constants
    const TYPE_NEW_TASKS = 'new_tasks';
    const TYPE_TASK_APPROVED = 'task_approved';
    const TYPE_TASK_REJECTED = 'task_rejected';
    const TYPE_PAYMENT_SUCCESS = 'payment_success';
    const TYPE_DEADLINE_WARNING = 'deadline_warning';
    const TYPE_BADGE_LEVELUP = 'badge_levelup';
    const TYPE_TASK_EXPIRED = 'task_expired';
    const TYPE_ADMIN_MESSAGE = 'admin_message';

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope to get read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Scope to get recent notifications.
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Mark this notification as read.
     */
    public function markAsRead(): void
    {
        if (!$this->read) {
            $this->update([
                'read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Get icon for notification type.
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_NEW_TASKS => '📋',
            self::TYPE_TASK_APPROVED => '✅',
            self::TYPE_TASK_REJECTED => '❌',
            self::TYPE_PAYMENT_SUCCESS => '💰',
            self::TYPE_DEADLINE_WARNING => '⏰',
            self::TYPE_BADGE_LEVELUP => '🏆',
            self::TYPE_TASK_EXPIRED => '⌛',
            self::TYPE_ADMIN_MESSAGE => '📢',
            default => '🔔',
        };
    }

    /**
     * Get color class for notification type.
     */
    public function getColorClassAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_NEW_TASKS => 'text-blue-600 bg-blue-50',
            self::TYPE_TASK_APPROVED => 'text-green-600 bg-green-50',
            self::TYPE_TASK_REJECTED => 'text-red-600 bg-red-50',
            self::TYPE_PAYMENT_SUCCESS => 'text-violet-600 bg-violet-50',
            self::TYPE_DEADLINE_WARNING => 'text-orange-600 bg-orange-50',
            self::TYPE_BADGE_LEVELUP => 'text-yellow-600 bg-yellow-50',
            self::TYPE_TASK_EXPIRED => 'text-zinc-600 bg-zinc-50',
            self::TYPE_ADMIN_MESSAGE => 'text-purple-600 bg-purple-50',
            default => 'text-zinc-600 bg-zinc-50',
        };
    }

    /**
     * Get the action URL for this notification.
     * Reads from data['action_url'], with smart fallback based on type.
     */
    public function getActionUrlAttribute(): ?string
    {
        // Prioritas 1: URL eksplisit dari data
        if (!empty($this->data['action_url'])) {
            return $this->data['action_url'];
        }

        // Prioritas 2: Fallback berdasarkan type
        $taskId = $this->data['task_id'] ?? null;

        return match ($this->type) {
            self::TYPE_TASK_APPROVED => $taskId ? "/user/task/{$taskId}/work" : '/user/my-tasks',
            self::TYPE_TASK_REJECTED => '/user/my-tasks',
            self::TYPE_PAYMENT_SUCCESS => '/user/my-tasks',
            self::TYPE_DEADLINE_WARNING => $taskId ? "/user/task/{$taskId}/work" : '/user/my-tasks',
            self::TYPE_NEW_TASKS => '/user/dashboard',
            self::TYPE_BADGE_LEVELUP => '/dashboard',
            self::TYPE_TASK_EXPIRED => '/user/my-tasks',
            self::TYPE_ADMIN_MESSAGE => $taskId ? "/user/task/{$taskId}/work" : '/dashboard',
            default => '/dashboard',
        };
    }
}
