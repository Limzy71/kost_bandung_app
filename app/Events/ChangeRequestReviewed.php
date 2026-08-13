<?php

namespace App\Events;

use App\Models\KostChangeRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChangeRequestReviewed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public KostChangeRequest $changeRequest,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->changeRequest->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'change.request.reviewed';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $kost = $this->changeRequest->kost;

        return [
            'kost_id' => $this->changeRequest->kost_id,
            'kost_slug' => $kost?->slug ?? '',
            'kost_name' => $kost?->name ?? 'Kost',
            'status' => $this->changeRequest->status,
            'review_note' => $this->changeRequest->review_note,
            'message' => $this->changeRequest->status === KostChangeRequest::STATUS_APPROVED
                ? 'Pengajuan perubahan untuk "'.($kost?->name ?? 'Kost').'" telah disetujui.'
                : 'Pengajuan perubahan untuk "'.($kost?->name ?? 'Kost').'" ditolak.',
        ];
    }
}
