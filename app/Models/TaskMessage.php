<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_task_id',
        'user_id',
        'sender_type',
        'message',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const SENDER_USER = 'user';
    const SENDER_ADMIN = 'admin';

    public function userTask(): BelongsTo
    {
        return $this->belongsTo(UserTask::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForAdmin($query)
    {
        return $query->where('sender_type', self::SENDER_ADMIN);
    }

    public function scopeForUser($query)
    {
        return $query->where('sender_type', self::SENDER_USER);
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }

    public function getFileUrl(): ?string
    {
        return $this->file_path ? \Storage::url($this->file_path) : null;
    }

    public function getFileSizeFormatted(): ?string
    {
        if (!$this->file_size) return null;

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->file_size;
        $unit = 0;

        while ($bytes >= 1024 && $unit < count($units) - 1) {
            $bytes /= 1024;
            $unit++;
        }

        return round($bytes, 2) . ' ' . $units[$unit];
    }

    public function isImage(): bool
    {
        return $this->file_type && str_starts_with($this->file_type, 'image/');
    }
}
