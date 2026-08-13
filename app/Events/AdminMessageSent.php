<?php

namespace App\Events;

use App\Models\AdminMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public AdminMessage $message,
    ) {}

    /**
     * Balasan admin masuk ke kanal private user yang bersangkutan;
     * pesan baru dari user masuk ke kanal private bersama seluruh admin.
     *
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        if ($this->message->sender_type === 'admin') {
            return [
                new PrivateChannel('App.Models.User.'.$this->message->conversation->user_id),
            ];
        }

        return [
            new PrivateChannel('admin.inbox'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'admin.message.sent';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_type' => $this->message->sender_type,
            'body' => $this->message->body,
            'created_at' => $this->message->created_at?->toISOString(),
        ];
    }
}
