<?php

namespace App\Events;

use App\Models\AdminConversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminConversationClosed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public AdminConversation $conversation,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->conversation->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'admin.conversation.closed';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'status' => 'closed',
            'closed_reason' => $this->conversation->closed_reason,
            'closed_at' => $this->conversation->closed_at?->toISOString(),
        ];
    }
}
