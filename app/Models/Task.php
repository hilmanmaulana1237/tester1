<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\CacheService;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'admin_id',
        'created_by',
        'title',
        'vcf_data',
        'description',
        'whatsapp_group_link',
        'tutorial_link',
        'difficulty_level',
        'expired_at',
        'is_expired',
        'priority_order',
        'estimated_amount',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_expired' => 'boolean',
        'priority_order' => 'integer',
        'estimated_amount' => 'decimal:2',
    ];

    const DIFFICULTY_EASY = 'easy';
    const DIFFICULTY_MEDIUM = 'medium';
    const DIFFICULTY_HARD = 'hard';

    const DIFFICULTIES = [
        self::DIFFICULTY_EASY => 'Easy',
        self::DIFFICULTY_MEDIUM => 'Medium',
        self::DIFFICULTY_HARD => 'Hard',
    ];

    protected static function booted(): void
    {
        static::creating(function ($task) {
            if (empty($task->created_by)) {
                $task->created_by = auth()->id();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Alias for createdBy for easier access in views
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function userTasks(): HasMany
    {
        return $this->hasMany(UserTask::class);
    }

    public function activeUserTask(): HasMany
    {
        return $this->hasMany(UserTask::class)->whereIn('status', ['taken', 'pending_verification_1', 'pending_verification_2']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_expired', false)
            ->where('expired_at', '>', now())
            ->whereHas('category', function ($q) {
                $q->where('is_active', true)
                    ->where('expired_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('is_expired', true)->orWhere('expired_at', '<=', now());
    }

    public function scopeForAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->whereDoesntHave('userTasks', function ($q) {
                $q->whereIn('status', ['taken', 'pending_verification_1', 'pending_verification_2']);
            });
    }

    /**
     * Scope untuk tugas yang belum pernah diambil/dibawa sama sekali oleh user
     */
    public function scopeNeverTaken($query)
    {
        return $query->whereDoesntHave('userTasks');
    }

    public function isExpired(): bool
    {
        return $this->is_expired || $this->expired_at < now();
    }

    public function isTaken(): bool
    {
        return $this->userTasks()
            ->whereIn('status', ['taken', 'pending_verification_1', 'pending_verification_2'])
            ->exists();
    }

    public function canBeEdited(): bool
    {
        return !$this->isTaken();
    }

    public function getDifficultyBadgeColorAttribute(): string
    {
        return match ($this->difficulty_level) {
            self::DIFFICULTY_EASY => 'success',
            self::DIFFICULTY_MEDIUM => 'warning',
            self::DIFFICULTY_HARD => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Boot method untuk auto-clear cache
     */
    protected static function boot()
    {
        parent::boot();

        // Clear available tasks count cache saat task berubah
        static::created(function () {
            CacheService::forget('available_tasks_count');
        });

        static::updated(function () {
            CacheService::forget('available_tasks_count');
        });

        static::deleted(function () {
            CacheService::forget('available_tasks_count');
        });
    }
}
