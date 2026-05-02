<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\CacheService;

class UserTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'status',
        'taken_at',
        'deadline_at',
        'cancelled_at',
        'completed_at',
        'failed_count',
        'verification_1_status',
        'verification_1_files',
        'verification_1_approved_by',
        'verification_1_approved_at',
        'verification_2_status',
        'verification_2_files',
        'verification_2_approved_by',
        'verification_2_approved_at',
        'payment_status',
        'payment_amount',
        'amount_change_reason',
        'payment_verified_by_admin_id',
        'payment_verified_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'deadline_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'verification_1_approved_at' => 'datetime',
        'verification_2_approved_at' => 'datetime',
        'payment_verified_at' => 'datetime',
        'failed_count' => 'integer',
        'payment_amount' => 'decimal:2',
        'verification_1_files' => 'array', // Cast JSON ke array
        'verification_2_files' => 'array', // Cast JSON ke array
    ];

    const STATUS_TAKEN = 'taken';
    const STATUS_PENDING_VERIFICATION_1 = 'pending_verification_1';
    const STATUS_PENDING_VERIFICATION_2 = 'pending_verification_2';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FAILED = 'failed';
    const STATUS_BANNED = 'banned';

    const STATUSES = [
        self::STATUS_TAKEN => 'Taken',
        self::STATUS_PENDING_VERIFICATION_1 => 'Pending Verification 1',
        self::STATUS_PENDING_VERIFICATION_2 => 'Pending Verification 2',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_FAILED => 'Failed',
        self::STATUS_BANNED => 'Banned',
    ];

    const PAYMENT_PENDING = 'pending';
    const PAYMENT_SUCCESS = 'success';
    const PAYMENT_FAILED = 'failed';

    const PAYMENT_STATUSES = [
        self::PAYMENT_PENDING => 'Pending',
        self::PAYMENT_SUCCESS => 'Success',
        self::PAYMENT_FAILED => 'Failed',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentVerifiedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by_admin_id');
    }

    public function verification1ApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verification_1_approved_by');
    }

    public function verification2ApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verification_2_approved_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TaskMessage::class);
    }

    public function unreadMessagesForUser(): HasMany
    {
        return $this->hasMany(TaskMessage::class)
            ->where('sender_type', TaskMessage::SENDER_ADMIN)
            ->where('is_read', false);
    }

    public function unreadMessagesForAdmin(): HasMany
    {
        return $this->hasMany(TaskMessage::class)
            ->where('sender_type', TaskMessage::SENDER_USER)
            ->where('is_read', false);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_TAKEN,
            self::STATUS_PENDING_VERIFICATION_1,
            self::STATUS_PENDING_VERIFICATION_2
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function isActive(): bool
    {
        return in_array($this->status, [
            self::STATUS_TAKEN,
            self::STATUS_PENDING_VERIFICATION_1,
            self::STATUS_PENDING_VERIFICATION_2
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->deadline_at && $this->deadline_at < now() && $this->isActive();
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_TAKEN => 'info',
            self::STATUS_PENDING_VERIFICATION_1 => 'warning',
            self::STATUS_PENDING_VERIFICATION_2 => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'secondary',
            self::STATUS_FAILED => 'danger',
            self::STATUS_BANNED => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Boot method untuk auto-clear cache
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache setelah create, update, delete
        static::created(function ($userTask) {
            CacheService::clearTaskCache();
            CacheService::forget(CacheService::userKey($userTask->user_id, 'tasks'));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'active_tasks'));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'my_active_tasks'));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'my_active_tasks_count'));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'dashboard_stats'));
            CacheService::forget('available_tasks_count'); // Clear badge count
        });

        static::updated(function ($userTask) {
            CacheService::clearTaskCache();
            CacheService::forget(CacheService::userKey($userTask->user_id, 'tasks'));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'active_tasks'));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'my_active_tasks'));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'my_active_tasks_count'));
            CacheService::forget(CacheService::taskKey($userTask->task_id));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'dashboard_stats'));
            CacheService::forget('available_tasks_count'); // Clear badge count
        });

        static::deleted(function ($userTask) {
            CacheService::clearTaskCache();
            CacheService::forget(CacheService::userKey($userTask->user_id, 'tasks'));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'active_tasks'));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'my_active_tasks'));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'my_active_tasks_count'));
            CacheService::forget(CacheService::userKey($userTask->user_id, 'dashboard_stats'));
            CacheService::forget('available_tasks_count'); // Clear badge count
        });
    }
}
