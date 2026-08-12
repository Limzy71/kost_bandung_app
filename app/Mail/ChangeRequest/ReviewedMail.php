<?php

namespace App\Mail\ChangeRequest;

use App\Models\Kost;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Kost $kost,
        public string $status,
        public ?string $note = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->status === 'approved'
                ? 'Perubahan Data Kost Anda Telah Disetujui'
                : 'Perubahan Data Kost Anda Ditolak',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: $this->status === 'approved'
                ? 'emails.changes.approved'
                : 'emails.changes.rejected',
            with: [
                'ownerName' => $this->kost->user->name,
                'kostName' => $this->kost->name,
                'note' => $this->note,
            ],
        );
    }
}
