<?php

use App\Livewire\Auth\VerifyAccount;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('verification hub screen can be rendered for unverified user', function () {
    $user = User::factory()->unverified()->create([
        'phone_number' => '081234567890',
        'terms_accepted_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertOk()
        ->assertSeeLivewire(VerifyAccount::class);
});

test('user can verify account using whatsapp otp code from verification hub', function () {
    $user = User::factory()->unverified()->create([
        'phone_number' => '081234567890',
        'terms_accepted_at' => now(),
        'role' => 'owner',
    ]);

    // Seed OTP
    $validOtp = '654321';
    Cache::put("phone_otp:{$user->id}", Hash::make($validOtp), now()->addMinutes(5));
    Cache::put("phone_otp_number:{$user->id}", '081234567890', now()->addMinutes(5));

    Livewire::actingAs($user)
        ->test(VerifyAccount::class)
        ->set('phoneOtp', $validOtp)
        ->call('verifyPhoneOtp')
        ->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->hasVerifiedEmail())->toBeTrue()
        ->and($user->isPhoneVerified())->toBeTrue();
});

test('user cannot verify with invalid otp code from verification hub', function () {
    $user = User::factory()->unverified()->create([
        'phone_number' => '081234567890',
        'terms_accepted_at' => now(),
    ]);

    Cache::put("phone_otp:{$user->id}", Hash::make('654321'), now()->addMinutes(5));
    Cache::put("phone_otp_number:{$user->id}", '081234567890', now()->addMinutes(5));

    Livewire::actingAs($user)
        ->test(VerifyAccount::class)
        ->set('phoneOtp', '111111')
        ->call('verifyPhoneOtp')
        ->assertSet('otpErrorMessage', 'Kode OTP yang Anda masukkan salah. Silakan periksa kembali.');

    $user->refresh();
    expect($user->hasVerifiedEmail())->toBeFalse()
        ->and($user->isPhoneVerified())->toBeFalse();
});

test('user can resend email verification from verification hub', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create([
        'terms_accepted_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(VerifyAccount::class)
        ->call('resendEmailVerification')
        ->assertDispatched('show-toast')
        ->assertSet('emailStatusMessage', 'Tautan verifikasi baru telah berhasil dikirimkan ke email Anda.');
});

test('already verified user visiting verification hub is redirected', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'role' => 'user',
        'terms_accepted_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertRedirect(route('home'));
});
