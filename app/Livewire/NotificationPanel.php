<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class NotificationPanel extends Component
{
    public $notifications;
    public $unreadCount = 0;
    public string $filter = 'all'; // 'all' atau 'unread'

    public function mount()
    {
        $this->loadNotifications();
    }

    // ── Cache keys ────────────────────────────────────────────────────────────
    private function cacheKey(string $suffix = ''): string
    {
        return 'notif_panel.' . Auth::id() . ($suffix ? '.' . $suffix : '');
    }

    private function clearCache(): void
    {
        Cache::forget($this->cacheKey('all'));
        Cache::forget($this->cacheKey('unread'));
        Cache::forget($this->cacheKey('count'));
    }

    // ── Load ─────────────────────────────────────────────────────────────────
    #[On('notification-created')]
    public function loadNotifications()
    {
        if (!Auth::check()) {
            $this->notifications = collect();
            $this->unreadCount   = 0;
            return;
        }

        $this->notifications = Cache::remember(
            $this->cacheKey('recent_40'),
            now()->addMinutes(2),
            function () {
                return Auth::user()->notifications()->recent(40)->get();
            }
        );

        $this->unreadCount = Cache::remember(
            $this->cacheKey('count'),
            now()->addMinute(),
            fn () => Auth::user()->unreadNotificationsCount()
        );
    }

    public function setFilter(string $filter)
    {
        $this->filter = $filter;
        // Do nothing else, since we are doing client-side filtering now
    }

    /**
     * Klik notifikasi → tandai dibaca → redirect ke action_url
     */
    public function openNotification($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if (!$notification) return;

        $notification->markAsRead();
        $this->clearCache();
        $this->loadNotifications();

        $url = $notification->action_url;
        if ($url) {
            return $this->redirect($url);
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->clearCache();
            // Don't reload: page navigates away immediately via JS
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update([
            'read'    => true,
            'read_at' => now(),
        ]);

        $this->clearCache();
        $this->loadNotifications();
    }

    /**
     * Hapus satu notifikasi.
     */
    public function deleteNotification($notificationId)
    {
        Auth::user()->notifications()->where('id', $notificationId)->delete();
        $this->clearCache();
        $this->loadNotifications();
    }

    /**
     * Hapus semua notifikasi yang sudah dibaca.
     */
    public function deleteAllRead()
    {
        Auth::user()->notifications()->read()->delete();
        $this->clearCache();
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-panel');
    }
}
