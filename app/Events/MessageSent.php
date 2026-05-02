<?php

namespace App\Events;

use App\Models\TaskMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userTaskId;

    public function __construct(TaskMessage $message)
    {
        $this->message = $message->load(['user:id,name,role']);
        $this->userTaskId = $message->user_task_id;

        \Log::info('MessageSent event created', [
            'user_task_id' => $this->userTaskId,
            'message_id' => $message->id,
            'channel' => 'chat.' . $this->userTaskId,
        ]);
    }

    public function broadcastOn(): Channel
    {
        $channel = new Channel('chat.' . $this->userTaskId);
        \Log::info('Broadcasting on channel: ' . $channel->name);
        return $channel;
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'user_id' => $this->message->user_id,
                'user_task_id' => $this->message->user_task_id, // CRITICAL: untuk validasi task
                'sender_type' => $this->message->sender_type,
                'message' => $this->message->message,
                'file_path' => $this->message->file_path,
                'file_name' => $this->message->file_name,
                'created_at' => $this->message->created_at->toISOString(),
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'role' => $this->message->user->role,
                ],
            ],
        ];
    }
}
