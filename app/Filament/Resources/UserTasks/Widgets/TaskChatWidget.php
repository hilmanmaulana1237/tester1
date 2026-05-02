<?php

namespace App\Filament\Resources\UserTasks\Widgets;

use App\Models\TaskMessage;
use App\Models\UserTask;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TaskChatWidget extends Widget
{
    protected string $view = 'filament.resources.user-tasks.widgets.task-chat-widget';

    public ?UserTask $record = null;

    protected int | string | array $columnSpan = 'full';

    public $messages = [];
    public $newMessage = '';

    public function mount(?UserTask $record = null): void
    {
        $this->record = $record;
        $this->loadMessages();
    }

    public function loadMessages(): void
    {
        if (!$this->record) {
            return;
        }

        $this->messages = $this->record->messages()
            ->with('user:id,name,role')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000',
        ]);

        TaskMessage::create([
            'user_task_id' => $this->record->id,
            'user_id' => Auth::id(),
            'sender_type' => TaskMessage::SENDER_ADMIN,
            'message' => trim($this->newMessage),
            'is_read' => false,
        ]);

        // Clear input after sending
        $this->reset('newMessage');
        $this->loadMessages();

        $this->dispatch('message-sent');
        $this->dispatch('scroll-to-bottom');
    }

    public function refreshMessages(): void
    {
        // Only reload messages, don't reset newMessage property
        $this->loadMessages();
        $this->markUserMessagesAsRead();

        // Skip reset to preserve user input
        $this->skipRender();
    }

    private function markUserMessagesAsRead(): void
    {
        if (!$this->record) {
            return;
        }

        // Mark user messages as read when admin opens chat
        $this->record->messages()
            ->where('sender_type', TaskMessage::SENDER_USER)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public static function canView(): bool
    {
        return Auth::check();
    }
}
