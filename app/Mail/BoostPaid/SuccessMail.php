<?php

namespace App\Mail\BoostPaid;

use App\Models\BoostTransaction;
use App\Models\Kost;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Kost $kost,
        public BoostTransaction $transaction
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran Berhasil - Boost Kost Anda Sekarang Aktif',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.boost.paid-success',
            with: [
                'ownerName' => $this->kost->user->name,
                'kostName' => $this->kost->name,
                'orderId' => $this->transaction->order_id,
                'paymentMethod' => $this->transaction->payment_type ?? 'Midtrans',
                'amount' => 'Rp '.number_format($this->transaction->amount, 0, ',', '.'),
                'paymentDate' => Carbon::parse($this->transaction->paid_at)->translatedFormat('d F Y H:i'),
                'expiryDate' => Carbon::parse($this->kost->boost_expires_at)->translatedFormat('l, d F Y H:i'),
            ],
        );
    }
}
