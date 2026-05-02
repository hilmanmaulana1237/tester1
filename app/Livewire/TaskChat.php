<?php

namespace App\Livewire;

use App\Models\TaskMessage;
use App\Models\UserTask;
use App\Models\SupportThread;
use App\Models\SupportMessage;
use App\Events\SupportMessageCreated;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class TaskChat extends Component
{
    use WithFileUploads;

    public $userTaskId;
    public ?UserTask $userTask = null;
    public string $newMessage = '';
    /** @var TemporaryUploadedFile|null */
    public $file;
    public array $messages = [];
    public $isTyping = false;
    public $lastMessageCount = 0;

    public function mount($userTaskId = null, $userTask = null)
    {
        // Support both userTaskId and userTask object
        if ($userTask instanceof UserTask) {
            $this->userTask = $userTask;
            $this->userTaskId = $userTask->id;
        } elseif ($userTaskId) {
            $this->userTaskId = $userTaskId;
            $this->userTask = UserTask::with(['user', 'task'])->findOrFail($userTaskId);
        }

        if ($this->userTask) {
            $this->loadMessages();
            $this->markMessagesAsRead();
            $this->lastMessageCount = count($this->messages);
        }
    }

    public function hydrate()
    {
        if ($this->userTaskId && !$this->userTask) {
            $this->userTask = UserTask::with(['user', 'task'])->find($this->userTaskId);
        }
    }

    /**
     * Polling method - called by Alpine JS fetch when count is different.
     */
    public function checkForNewMessages(int $newCount)
    {
        $this->lastMessageCount = $newCount;
        
        // Clear cache and reload
        cache()->forget("chat_messages_{$this->userTask->id}");
        $this->loadMessages();
        $this->markMessagesAsRead();
    }

    public function loadMessages()
    {
        if (!$this->userTask) {
            $this->messages = [];
            return;
        }

        // Cache messages for 5 seconds
        $cacheKey = "chat_messages_{$this->userTask->id}";

        $this->messages = cache()->remember($cacheKey, 5, function () {
            return $this->userTask->messages()
                ->with('user:id,name,role')
                ->orderBy('created_at', 'asc')
                ->get()
                ->toArray();
        });

        $this->dispatch('messages-loaded');
    }

    public function sendMessage(?string $text = null)
    {
        if (!$this->userTask) {
            $this->addError('newMessage', 'User task not found.');
            return;
        }

        $this->validate([
            'newMessage' => 'nullable',
            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip',
        ]);

        $messageText = trim($text ?? $this->newMessage);
        
        if (empty($messageText) && !$this->file) {
            $this->validate([
                'newMessage' => 'required',
            ]);
            return;
        }

        $filePath = null;
        $fileName = null;
        $fileType = null;
        $fileSize = null;

        // Upload file jika ada
        if ($this->file) {
            $filePath = $this->file->store('task-messages', 'public');
            $fileName = $this->file->getClientOriginalName();
            $fileType = $this->file->getMimeType();
            $fileSize = $this->file->getSize();
        }

        $message = TaskMessage::create([
            'user_task_id' => $this->userTask->id,
            'user_id' => Auth::id(),
            'sender_type' => Auth::user()->role === 'user'
                ? TaskMessage::SENDER_USER
                : TaskMessage::SENDER_ADMIN,
            'message' => $messageText ?: null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'is_read' => false,
        ]);

        // Sinkronisasi ke SupportThread + SupportMessage supaya masuk ke panel admin
        $thread = SupportThread::updateOrCreate(
            [
                'task_id' => $this->userTask->task_id,
                'user_id' => $this->userTask->user_id,
            ],
            [
                'category_id' => $this->userTask->task->category_id ?? null,
                'title' => $this->userTask->task->title ?? 'Task',
                'status' => 'open',
                'admin_id' => Auth::user()->isAdmin() ? Auth::id() : null,
                'last_message_at' => now(),
            ]
        );

        $supportMessage = SupportMessage::create([
            'support_thread_id' => $thread->id,
            'sender_id' => Auth::id(),
            'sender_role' => Auth::user()->role === 'user' ? 'user' : 'admin',
            'message' => $messageText ?: '',
            'meta' => array_filter([
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'task_message_id' => $message->id,
            ]),
        ]);

        try {
            SupportMessageCreated::dispatch($supportMessage);
        } catch (\Exception $e) {
            Log::error('Support broadcast failed: ' . $e->getMessage());
        }

        // Kirim notifikasi ke user bahwa admin membalas (hanya jika pengirim adalah admin)
        if (Auth::user()->isAdmin() && $this->userTask->user_id !== Auth::id()) {
            try {
                \App\Services\NotificationService::notifyChatMessage(
                    $this->userTask->user_id,
                    $this->userTask->task_id,
                    Auth::user()->name,
                    $messageText
                );
            } catch (\Exception $e) {
                Log::error('Chat notification failed: ' . $e->getMessage());
            }
        }

        // Broadcast event untuk real-time
        try {
            Log::info('Broadcasting message to chat.' . $this->userTask->id, [
                'message_id' => $message->id,
                'user_task_id' => $message->user_task_id,
            ]);

            broadcast(new MessageSent($message));

            Log::info('Broadcast successful for message ' . $message->id);
        } catch (\Exception $e) {
            Log::error('Reverb broadcast failed: ' . $e->getMessage());
        }

        // Clear cache untuk task ini agar pesan baru langsung muncul
        cache()->forget("chat_messages_{$this->userTask->id}");

        // Reset message dan file
        $this->reset(['newMessage', 'file']);
        $this->loadMessages();

        // Emit events untuk UI update
        $this->dispatch('message-sent', taskId: $this->userTask->id);
        $this->dispatch('messages-loaded');

        // Trigger scroll to bottom
        $this->js('window.dispatchEvent(new CustomEvent("message-received"))');

        // Show notification untuk admin
        if (Auth::user()->role !== 'user') {
            Notification::make()
                ->title('Message sent')
                ->body('Your message has been sent successfully')
                ->success()
                ->send();
        }
    }

    public function removeFile()
    {
        $this->file = null;
    }

    #[On('refresh-messages')]
    public function refreshMessages($scrollToBottom = false)
    {
        // Clear cache untuk get pesan terbaru
        cache()->forget("chat_messages_{$this->userTask->id}");

        $oldCount = count($this->messages);
        $this->loadMessages();
        $newCount = count($this->messages);

        // Hanya scroll ke bawah jika ada pesan baru
        if ($scrollToBottom || $newCount > $oldCount) {
            $this->dispatch('messages-loaded');
        }

        $this->markMessagesAsRead();

        // Notify user if new messages arrived
        if ($newCount > $this->lastMessageCount && Auth::user()->role !== 'user') {
            $diff = $newCount - $this->lastMessageCount;
            Notification::make()
                ->title('New messages')
                ->body("{$diff} new message(s) received")
                ->info()
                ->send();
        }

        $this->lastMessageCount = $newCount;
    }

    // Method untuk refresh dari JavaScript Echo listener
    public function messageReceived(array $event)
    {
        if (!isset($event['message'])) return;

        // VALIDASI: Jangan tambahkan jika dari user yang sama
        if (auth()->id() === $event['message']['user_id']) return;

        // VALIDASI: Pastikan message dari userTask yang benar (CRITICAL!)
        if (!$this->userTask || $event['message']['user_task_id'] !== $this->userTask->id) {
            return;
        }

        // Tambahkan missing data yang diperlukan untuk render
        $newMessage = $event['message'];

        // Pastikan format created_at konsisten
        if (!isset($newMessage['created_at'])) {
            $newMessage['created_at'] = now()->toISOString();
        }

        // Tambahkan ke array messages
        $this->messages[] = $newMessage;

        // Clear cache agar loadMessages() dapat pesan terbaru
        cache()->forget("chat_messages_{$this->userTask->id}");

        // Dispatch event untuk trigger scroll
        $this->dispatch('messages-loaded');

        // Force Livewire to re-render
        $this->js('setTimeout(() => window.dispatchEvent(new CustomEvent("message-received")), 100)');
    }

    private function markMessagesAsRead()
    {
        if (!$this->userTask) {
            return;
        }

        // Mark messages as read based on user role
        if (Auth::user()->role === 'user') {
            // User marks admin messages as read
            $this->userTask->messages()
                ->where('sender_type', TaskMessage::SENDER_ADMIN)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        } else {
            // Admin marks user messages as read
            $this->userTask->messages()
                ->where('sender_type', TaskMessage::SENDER_USER)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }
    }

    public function render()
    {
        return view('livewire.task-chat');
    }
}
