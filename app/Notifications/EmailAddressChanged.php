<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailAddressChanged extends Notification
{
    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Alamat Email Akun Anda Telah Diubah — KostBandung')
            ->greeting('Halo,')
            ->line('Alamat email untuk akun **KostBandung** Anda baru saja diubah.')
            ->line('Jika Anda tidak melakukan perubahan ini, segera hubungi tim dukungan kami dan amankan akun Anda.')
            ->salutation("Salam hangat,\nTim KostBandung");
    }
}
