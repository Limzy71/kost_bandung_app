<?php

use App\Models\User;
use App\Notifications\EmailAddressChanged;
use App\Notifications\VerifyNewEmailAddress;

it('renders the new email verification mail', function () {
    $user = User::factory()->create(['role' => 'user']);

    $message = (new VerifyNewEmailAddress)->toMail($user);

    expect($message->subject)->toBe('Konfirmasi Alamat Email Baru Anda');
    expect($message->introLines[0])->toContain('akun KostBandung.web.id');
    expect($message->actionText)->toBe('Konfirmasi Alamat Email');
    expect($message->actionUrl)->toContain(route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)], false));
});

it('renders the email address changed notice', function () {
    $message = (new EmailAddressChanged)->toMail(new stdClass);

    expect($message->subject)->toBe('Alamat Email Akun Anda Telah Diubah');
    expect($message->introLines)->toContain('Alamat email untuk akun KostBandung.web.id Anda baru saja diubah.');
});
