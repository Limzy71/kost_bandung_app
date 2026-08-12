<?php

namespace App\Mail\BoostPaid;

use App\Models\Kost;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Kost $kost) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Masa Aktif Boost Kost Anda Berakhir dalam 3 Hari',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.boost.paid-reminder',
            with: [
                'ownerName' => $this->kost->user->name,
                'kostName' => $this->kost->name,
                'expiryDate' => Carbon::parse($this->kost->boost_expires_at)->translatedFormat('l, d F Y H:i'),
                'price' => 'Rp '.number_format(config('midtrans.boost_price', 50000), 0, ',', '.'),
            ],
        );
    }
}
