<?php

use App\Models\User;
use App\Notifications\EmailAddressChanged;
use App\Notifications\VerifyNewEmailAddress;

it('renders the new email verification mail', function () {
    $user = User::factory()->create(['role' => 'user']);

    $message = (new VerifyNewEmailAddress)->toMail($user);

    expect($message->subject)->toBe('Konfirmasi Alamat Email Baru Anda — KostBandung');
    expect($message->introLines[0])->toContain('akun **KostBandung**');
    expect($message->actionText)->toBe('Konfirmasi Alamat Email');
    expect($message->actionUrl)->toContain(route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)], false));
});

it('renders the email address changed notice', function () {
    $message = (new EmailAddressChanged)->toMail(new stdClass);

    expect($message->subject)->toBe('Alamat Email Akun Anda Telah Diubah — KostBandung');
    expect($message->introLines)->toContain('Alamat email untuk akun **KostBandung** Anda baru saja diubah.');
});
