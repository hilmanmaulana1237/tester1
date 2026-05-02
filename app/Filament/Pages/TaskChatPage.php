<?php

namespace App\Filament\Pages;

use App\Models\UserTask;
use App\Models\TaskMessage;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class TaskChatPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.task-chat-page';

    protected static bool $shouldRegisterNavigation = false;

    public ?UserTask $userTask = null;

    public function mount($record): void
    {
        $this->userTask = UserTask::with(['user', 'task'])->findOrFail($record);

        // Mark messages as read when admin opens chat
        $this->userTask->messages()
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function getTitle(): string
    {
        return $this->userTask ? "Chat: {$this->userTask->user->name}" : 'Chat';
    }

    public function getHeading(): string
    {
        return $this->userTask ? "💬 Chat dengan {$this->userTask->user->name}" : 'Chat';
    }

    public function getSubheading(): ?string
    {
        return $this->userTask ? $this->userTask->task->title : null;
    }
}
