<?php

namespace App\Livewire\Admin;

use App\Events\SupportMessageCreated;
use App\Models\SupportMessage;
use App\Models\SupportThread;
use App\Models\TaskMessage;
use App\Models\UserTask;
use App\Models\Category;
use App\Events\MessageSent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SupportChat extends Component
{
    public string $search = '';
    public ?int $categoryFilter = null;
    public ?int $activeThreadId = null;
    public string $newMessage = '';
    public int $messageCount = 0;
    public bool $showMobileChat = false;
    public string $statusFilter = 'open'; // open, closed, all
    public bool $isPrivateNote = false;

    public function mount(): void
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403);
        }

        $first = $this->threads->first();
        $this->activeThreadId = $first?->id;
        $this->messageCount = $this->getActiveMessageCount();
    }

    /**
     * Polling method - called by Alpine JS fetch when count is different.
     */
    public function checkForNewMessages(int $newCount): void
    {
        $this->messageCount = $newCount;
        $this->dispatch('new-message-arrived');
    }

    private function getActiveMessageCount(): int
    {
        if (!$this->activeThreadId) return 0;
        return SupportMessage::where('support_thread_id', $this->activeThreadId)->count();
    }

    public function setThread(int $threadId): void
    {
        // Security Check: Ensure admin has rights to view this thread
        $exists = $this->applyAdminIsolation(SupportThread::where('id', $threadId))->exists();
        if (!$exists) {
            abort(403, 'Unauthorized access to this support thread.');
        }

        $this->activeThreadId = $threadId;
        $this->messageCount = $this->getActiveMessageCount();
        $this->showMobileChat = true;
    }

    public function backToList(): void
    {
        $this->showMobileChat = false;
    }

    public function sendMessage(?string $text = null): void
    {
        $messageText = trim($text ?? $this->newMessage);
        
        if (empty($messageText)) {
            $this->validate(['newMessage' => ['required', 'string', 'max:5000']]);
            return;
        }
        
        if (strlen($messageText) > 5000) {
            $this->addError('newMessage', 'The message may not be greater than 5000 characters.');
            return;
        }

        $thread = SupportThread::query()->findOrFail($this->activeThreadId);

        $message = SupportMessage::create([
            'support_thread_id' => $thread->id,
            'sender_id' => Auth::id(),
            'sender_role' => 'admin',
            'message' => $messageText,
            'meta' => $this->isPrivateNote ? ['is_private' => true] : null,
        ]);

        $thread->update([
            'admin_id' => Auth::id(),
            'last_message_at' => now(),
        ]);

        // Private notes are admin-only: skip task message mirroring and notifications
        if (!$this->isPrivateNote) {
            $userTask = UserTask::where('task_id', $thread->task_id)
                ->where('user_id', $thread->user_id)
                ->latest()
                ->first();

            if ($userTask) {
                $taskMessage = TaskMessage::create([
                    'user_task_id' => $userTask->id,
                    'user_id' => Auth::id(),
                    'sender_type' => TaskMessage::SENDER_ADMIN,
                    'message' => $messageText,
                    'is_read' => false,
                ]);

                try {
                    broadcast(new MessageSent($taskMessage));
                } catch (\Exception $e) {
                    Log::error('Broadcast failed: ' . $e->getMessage());
                }
            }
        }

        try {
            SupportMessageCreated::dispatch($message);
        } catch (\Exception $e) {
            Log::error('Support broadcast failed: ' . $e->getMessage());
        }

        $this->newMessage = '';
        $this->isPrivateNote = false;
        $this->messageCount = $this->getActiveMessageCount();
        $this->clearCache();

        // Kirim notifikasi ke user bahwa admin membalas (skip for private notes)
        if (!$this->isPrivateNote) {
            try {
                $thread = SupportThread::find($this->activeThreadId);
                if ($thread && $thread->user_id && $thread->user_id !== auth()->id()) {
                    \App\Services\NotificationService::notifyChatMessage(
                        $thread->user_id,
                        $thread->task_id ?? 0,
                        auth()->user()->name,
                        $messageText
                    );
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Chat notification failed: ' . $e->getMessage());
            }
        }

        $this->dispatch('message-sent');
    }

    public function closeThread(): void
    {
        if (!$this->activeThreadId) return;
        SupportThread::whereKey($this->activeThreadId)->update(['status' => 'closed']);
        $this->clearCache();
    }

    public function reopenThread(): void
    {
        if (!$this->activeThreadId) return;
        SupportThread::whereKey($this->activeThreadId)->update(['status' => 'open']);
        $this->clearCache();
    }
    
    public function markAllAsRead(): void
    {
        $adminIsolationQuery = $this->applyAdminIsolation(SupportThread::query());
        $allowedThreadIds = $adminIsolationQuery->pluck('id');

        SupportMessage::where('sender_role', 'user')
            ->whereIn('support_thread_id', $allowedThreadIds)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->clearCache();
        
        if ($this->activeThreadId) {
            $this->messageCount = $this->getActiveMessageCount();
        }
    }
    
    private function clearCache(): void
    {
        cache()->forget('admin_support_chat_stats_' . Auth::id());
        cache()->forget('support_chat_category_stats_' . Auth::id());
    }

    /**
     * Apply admin thread isolation scope (Security)
     */
    private function applyAdminIsolation($query)
    {
        if (Auth::check() && !Auth::user()->isSuperAdmin()) {
            $adminId = Auth::id();
            $query->where(function ($q) use ($adminId) {
                // Rule 1: Threads connected to Tasks owned by this admin (or unassigned tasks)
                $q->whereHas('task', function ($sub) use ($adminId) {
                    $sub->where('admin_id', $adminId)->orWhereNull('admin_id');
                })
                // Rule 2: General support threads without tasks
                ->orWhereNull('task_id');
            });
        }
        return $query;
    }

    /**
     * Get category stats for sidebar: total threads, open count, today's active count.
     */
    public function getCategoryStatsProperty(): Collection
    {
        return cache()->remember('support_chat_category_stats_' . Auth::id(), now()->addSeconds(60), function () {
            return Category::orderBy('name')
                ->withCount([
                    'supportThreads as total_count' => function ($q) {
                        $this->applyAdminIsolation($q);
                    },
                    'supportThreads as open_count' => function ($q) {
                        $this->applyAdminIsolation($q)->where('status', 'open');
                    },
                    'supportThreads as today_active' => function ($q) {
                        $this->applyAdminIsolation($q)->where('last_message_at', '>=', today());
                    }
                ])
                ->get();
        });
    }

    /**
     * Get global stats for the header overview.
     */
    public function getStatsProperty(): array
    {
        return cache()->remember('admin_support_chat_stats_' . Auth::id(), now()->addSeconds(30), function () {
            return [
                'total' => $this->applyAdminIsolation(SupportThread::query())->count(),
                'open' => $this->applyAdminIsolation(SupportThread::where('status', 'open'))->count(),
                'closed' => $this->applyAdminIsolation(SupportThread::where('status', 'closed'))->count(),
                'today' => $this->applyAdminIsolation(SupportThread::where('last_message_at', '>=', today()))->count(),
                'unread' => SupportMessage::where('sender_role', 'user')
                    ->whereHas('thread', function ($q) {
                        $this->applyAdminIsolation($q);
                    })
                    ->whereNull('read_at')
                    ->count(),
            ];
        });
    }

    #[Computed]
    public function threads(): Collection
    {
        $query = SupportThread::query()->with(['task', 'category', 'user', 'latestMessage']);
        
        $query = $this->applyAdminIsolation($query);

        return $query
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->statusFilter !== 'all', fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('task', fn($q2) => $q2->where('title', 'like', "%{$this->search}%"));
                });
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->limit(50)
            ->get();
    }

    public function fetchActiveThread(): ?SupportThread
    {
        if (!$this->activeThreadId) return null;

        $thread = SupportThread::with([
            'task', 
            'category', 
            'user'
        ])->find($this->activeThreadId);

        if ($thread) {
            // Capped to latest 100 messages to ensure lightning-fast thread switching
            $messages = SupportMessage::with('sender')
                ->where('support_thread_id', $this->activeThreadId)
                ->latest()
                ->limit(100)
                ->get()
                ->reverse()
                ->values();
                
            $thread->setRelation('messages', $messages);
        }

        return $thread;
    }

    public function render()
    {
        return view('admin.support-chat', [
            'threads' => $this->threads,
            'activeThread' => $this->fetchActiveThread(),
            'categoryStats' => $this->categoryStats,
            'stats' => $this->stats,
        ])->layout('layouts.support-chat');
    }
}
