<?php

namespace App\Notifications;

use App\Models\KostChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KostChangeReviewed extends Notification
{
    use Queueable;

    public function __construct(
        public KostChangeRequest $changeRequest,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $kost = $this->changeRequest->kost;

        return [
            'kost_id' => $this->changeRequest->kost_id,
            'kost_slug' => $kost?->slug ?? '',
            'kost_name' => $kost?->name ?? 'Kost',
            'status' => $this->changeRequest->status,
            'review_note' => $this->changeRequest->review_note,
        ];
    }
}
