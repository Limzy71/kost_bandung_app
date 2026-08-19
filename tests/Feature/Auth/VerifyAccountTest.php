<?php

use App\Livewire\Auth\VerifyAccount;
use App\Models\User;
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

test('user verifying whatsapp otp marks phone as verified', function () {
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
        ->assertDispatched('show-toast');

    $user->refresh();
    expect($user->isPhoneVerified())->toBeTrue();
});

test('user with both email and phone verified can proceed to destination via button', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone_number' => '081234567890',
        'phone_verified_at' => null,
        'terms_accepted_at' => now(),
        'role' => 'owner',
    ]);

    $validOtp = '654321';
    Cache::put("phone_otp:{$user->id}", Hash::make($validOtp), now()->addMinutes(5));
    Cache::put("phone_otp_number:{$user->id}", '081234567890', now()->addMinutes(5));

    Livewire::actingAs($user)
        ->test(VerifyAccount::class)
        ->set('phoneOtp', $validOtp)
        ->call('verifyPhoneOtp')
        ->assertDispatched('show-toast')
        ->call('completeAndProceed')
        ->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->isFullyVerified())->toBeTrue();
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
    expect($user->isPhoneVerified())->toBeFalse();
});

test('user can change wrong phone number during verification', function () {
    $user = User::factory()->unverified()->create([
        'phone_number' => '081234567890',
        'terms_accepted_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(VerifyAccount::class)
        ->call('toggleEditPhone')
        ->assertSet('isEditingPhone', true)
        ->set('newPhoneNumber', '089876543210')
        ->call('updatePhoneNumber')
        ->assertDispatched('show-toast')
        ->assertSet('isEditingPhone', false);

    $user->refresh();
    expect($user->phone_number)->toBe('089876543210')
        ->and($user->isPhoneVerified())->toBeFalse();
});

test('user can change wrong email address during verification', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create([
        'email' => 'wrongtypo@example.com',
        'terms_accepted_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(VerifyAccount::class)
        ->call('toggleEditEmail')
        ->assertSet('isEditingEmail', true)
        ->set('newEmail', 'correct@example.com')
        ->call('updateEmail')
        ->assertDispatched('show-toast')
        ->assertSet('isEditingEmail', false);

    $user->refresh();
    expect($user->email)->toBe('correct@example.com')
        ->and($user->hasVerifiedEmail())->toBeFalse();
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

test('already verified user visiting verification hub can render and proceed', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
        'role' => 'user',
        'terms_accepted_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertOk()
        ->assertSeeLivewire(VerifyAccount::class);

    Livewire::actingAs($user)
        ->test(VerifyAccount::class)
        ->call('completeAndProceed')
        ->assertRedirect(route('home'));
});
