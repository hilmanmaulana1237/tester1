<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'expired_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->created_by)) {
                $category->created_by = auth()->id();
            }
        });
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function activeTasks(): HasMany
    {
        return $this->hasMany(Task::class)->where('is_expired', false);
    }

    public function userTasks(): HasManyThrough
    {
        return $this->hasManyThrough(UserTask::class, Task::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('expired_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expired_at', '<=', now());
    }

    public function scopeForAdmin($query, $adminId)
    {
        return $query->where('created_by_admin_id', $adminId);
    }

    public function isExpired(): bool
    {
        return $this->expired_at < now();
    }

    public function getActiveTasksCountAttribute(): int
    {
        return $this->activeTasks()->count();
    }

    public function supportThreads(): HasMany
    {
        return $this->hasMany(SupportThread::class);
    }
}
