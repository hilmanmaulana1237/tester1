<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERADMIN = 'superadmin';

    const BADGE_NONE = 'none';
    const BADGE_JUNIOR = 'junior';
    const BADGE_SENIOR = 'senior';
    const BADGE_GOD = 'god';
    const BADGE_PREMIUM_ADMIN = 'premium_admin';

    // Badge thresholds based on total earnings (in Rupiah)
    const BADGE_THRESHOLD_JUNIOR = 100000;    // Rp 100.000
    const BADGE_THRESHOLD_SENIOR = 500000;    // Rp 500.000
    const BADGE_THRESHOLD_GOD = 1000000;      // Rp 1.000.000

    const EWALLET_GOPAY = 'gopay';
    const EWALLET_OVO = 'ovo';
    const EWALLET_DANA = 'dana';
    const EWALLET_SHOPEEPAY = 'shopeepay';
    const EWALLET_LINKAJA = 'linkaja';

    const ROLES = [
        self::ROLE_USER => 'User',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_SUPERADMIN => 'Super Admin',
    ];

    const BADGES = [
        self::BADGE_NONE => 'None',
        self::BADGE_JUNIOR => 'Junior',
        self::BADGE_SENIOR => 'Senior',
        self::BADGE_GOD => 'God',
        self::BADGE_PREMIUM_ADMIN => 'Premium Admin',
    ];

    const EWALLETS = [
        self::EWALLET_GOPAY => 'GoPay',
        self::EWALLET_OVO => 'OVO',
        self::EWALLET_DANA => 'DANA',
        self::EWALLET_SHOPEEPAY => 'ShopeePay',
        self::EWALLET_LINKAJA => 'LinkAja',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_banned',
        'ban_until',
        'badge',
        'failed_task_count',
        'ewallet_type',
        'ewallet_number',
        'ewallet_name',
        'phone',
        'whatsapp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ban_until' => 'datetime',
            'is_banned' => 'boolean',
            'failed_task_count' => 'integer',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_SUPERADMIN;
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    /**
     * Check if user is banned
     */
    public function isBanned(): bool
    {
        return $this->is_banned && (!$this->ban_until || $this->ban_until > now());
    }

    /**
     * Check if user has premium admin badge
     */
    public function isPremiumAdmin(): bool
    {
        return $this->badge === self::BADGE_PREMIUM_ADMIN;
    }

    /**
     * Get all user tasks for this user
     */
    public function userTasks()
    {
        return $this->hasMany(UserTask::class);
    }

    /**
     * Get all notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get unread notifications count
     */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Get total earnings from completed tasks with successful payment
     */
    public function getTotalEarnings(): int
    {
        return (int) $this->userTasks()
            ->where('status', UserTask::STATUS_COMPLETED)
            ->where('payment_status', 'success')
            ->sum('payment_amount');
    }

    /**
     * Calculate badge based on total earnings
     */
    public function calculateBadgeFromEarnings(): string
    {
        // Premium admin badge cannot be changed automatically
        if ($this->badge === self::BADGE_PREMIUM_ADMIN) {
            return self::BADGE_PREMIUM_ADMIN;
        }

        $totalEarnings = $this->getTotalEarnings();

        if ($totalEarnings >= self::BADGE_THRESHOLD_GOD) {
            return self::BADGE_GOD;
        } elseif ($totalEarnings >= self::BADGE_THRESHOLD_SENIOR) {
            return self::BADGE_SENIOR;
        } elseif ($totalEarnings >= self::BADGE_THRESHOLD_JUNIOR) {
            return self::BADGE_JUNIOR;
        }

        return self::BADGE_NONE;
    }

    /**
     * Update badge based on total earnings
     */
    public function updateBadgeBasedOnEarnings(): bool
    {
        $newBadge = $this->calculateBadgeFromEarnings();

        if ($this->badge !== $newBadge) {
            $this->badge = $newBadge;
            return $this->save();
        }

        return false;
    }

    /**
     * Check if user can take tasks
     * Admin users are not allowed to take tasks
     */
    public function canTakeTask(): bool
    {
        // Admins cannot take tasks
        if ($this->isAdmin()) {
            return false;
        }

        // Banned users cannot take tasks
        if ($this->isBanned()) {
            return false;
        }

        return true;
    }

    /**
     * Get reason why user cannot take task
     */
    public function getCannotTakeTaskReason(): ?string
    {
        if ($this->isAdmin()) {
            return 'Admin tidak diperbolehkan mengambil task.';
        }

        if ($this->isBanned()) {
            return 'Akun Anda sedang dibanned.';
        }

        return null;
    }

    /**
     * Get badge label for display
     */
    public function getBadgeLabel(): string
    {
        return match ($this->badge) {
            self::BADGE_JUNIOR => 'Junior',
            self::BADGE_SENIOR => 'Senior',
            self::BADGE_GOD => 'God',
            self::BADGE_PREMIUM_ADMIN => 'Premium Admin',
            default => 'None',
        };
    }

    /**
     * Get badge color for display
     */
    public function getBadgeColor(): string
    {
        return match ($this->badge) {
            self::BADGE_JUNIOR => 'info',
            self::BADGE_SENIOR => 'warning',
            self::BADGE_GOD => 'success',
            self::BADGE_PREMIUM_ADMIN => 'primary',
            default => 'gray',
        };
    }

    /**
     * Get earnings needed for next badge
     */
    public function getEarningsForNextBadge(): ?array
    {
        $currentEarnings = $this->getTotalEarnings();

        if ($this->badge === self::BADGE_PREMIUM_ADMIN || $this->badge === self::BADGE_GOD) {
            return null; // Already at max
        }

        if ($currentEarnings < self::BADGE_THRESHOLD_JUNIOR) {
            return [
                'next_badge' => 'Junior',
                'needed' => self::BADGE_THRESHOLD_JUNIOR - $currentEarnings,
                'target' => self::BADGE_THRESHOLD_JUNIOR,
            ];
        } elseif ($currentEarnings < self::BADGE_THRESHOLD_SENIOR) {
            return [
                'next_badge' => 'Senior',
                'needed' => self::BADGE_THRESHOLD_SENIOR - $currentEarnings,
                'target' => self::BADGE_THRESHOLD_SENIOR,
            ];
        } elseif ($currentEarnings < self::BADGE_THRESHOLD_GOD) {
            return [
                'next_badge' => 'God',
                'needed' => self::BADGE_THRESHOLD_GOD - $currentEarnings,
                'target' => self::BADGE_THRESHOLD_GOD,
            ];
        }

        return null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }
}
