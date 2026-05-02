<?php

namespace App\Events;

use App\Models\SupportMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportMessageCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SupportMessage $message)
    {
        $this->message->loadMissing(['sender']);
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('support.thread.' . $this->message->support_thread_id);
    }

    public function broadcastAs(): string
    {
        return 'SupportMessageCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'thread_id' => $this->message->support_thread_id,
            'sender_id' => $this->message->sender_id,
            'sender_role' => $this->message->sender_role,
            'message' => $this->message->message,
            'created_at' => $this->message->created_at?->toIso8601String(),
            'sender_name' => $this->message->sender?->name,
        ];
    }
}
