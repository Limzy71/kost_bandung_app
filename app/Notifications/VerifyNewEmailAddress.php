<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyNewEmailAddress extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Konfirmasi Alamat Email Baru Anda')
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Alamat email untuk akun KostBandung.web.id Anda baru saja diubah menjadi '.$notifiable->email.'.')
            ->line('Silakan klik tombol di bawah untuk mengonfirmasi alamat email baru Anda.')
            ->action('Konfirmasi Alamat Email', $verificationUrl)
            ->line('Jika Anda tidak melakukan perubahan ini, harap segera hubungi kami agar akun Anda diamankan.');
    }

    /**
     * Get the verification URL for the given notifiable (based on the new email).
     */
    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
