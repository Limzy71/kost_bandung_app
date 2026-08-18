<?php

use App\Livewire\Profile\Index as ProfileIndex;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    RateLimiter::clear('phone_otp_limit:1');
    Cache::flush();
});

test('user model helper isPhoneVerified returns expected boolean', function () {
    $unverifiedUser = User::factory()->create([
        'phone_number' => '081234567890',
        'phone_verified_at' => null,
    ]);

    $verifiedUser = User::factory()->create([
        'phone_number' => '081234567891',
        'phone_verified_at' => now(),
    ]);

    expect($unverifiedUser->isPhoneVerified())->toBeFalse()
        ->and($verifiedUser->isPhoneVerified())->toBeTrue();
});

test('whatsapp service normalizes indonesian phone numbers correctly', function () {
    expect(WhatsAppService::normalizePhoneNumber('081234567890'))->toBe('6281234567890')
        ->and(WhatsAppService::normalizePhoneNumber('+6281234567890'))->toBe('6281234567890')
        ->and(WhatsAppService::normalizePhoneNumber('6281234567890'))->toBe('6281234567890')
        ->and(WhatsAppService::normalizePhoneNumber('81234567890'))->toBe('6281234567890')
        ->and(WhatsAppService::normalizePhoneNumber('0812-3456-7890'))->toBe('6281234567890');
});

test('user can request phone otp via livewire profile component', function () {
    $user = User::factory()->create([
        'phone_number' => '081234567890',
        'phone_verified_at' => null,
    ]);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->call('sendPhoneOtp')
        ->assertSet('showOtpModal', true)
        ->assertSet('otpSent', true)
        ->assertDispatched('show-toast');

    expect(Cache::has("phone_otp:{$user->id}"))->toBeTrue()
        ->and(Cache::get("phone_otp_number:{$user->id}"))->toBe('081234567890');
});

test('user cannot request otp if phone number is empty', function () {
    $user = User::factory()->create([
        'phone_number' => null,
        'phone_verified_at' => null,
    ]);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->call('sendPhoneOtp')
        ->assertSet('showOtpModal', false);
});

test('phone otp requests are rate limited to 3 attempts per window', function () {
    $user = User::factory()->create([
        'phone_number' => '081234567890',
        'phone_verified_at' => null,
    ]);

    $service = new WhatsAppService;

    // 1st, 2nd, 3rd attempts should succeed
    expect($service->sendOtp($user)['success'])->toBeTrue()
        ->and($service->sendOtp($user)['success'])->toBeTrue()
        ->and($service->sendOtp($user)['success'])->toBeTrue();

    // 4th attempt should be blocked by rate limiter
    $fourth = $service->sendOtp($user);
    expect($fourth['success'])->toBeFalse()
        ->and($fourth['cooldown'])->toBeGreaterThan(0);
});

test('user can verify phone with correct otp code', function () {
    $user = User::factory()->create([
        'phone_number' => '081234567890',
        'phone_verified_at' => null,
    ]);

    // Seed OTP in cache
    $validOtp = '123456';
    Cache::put("phone_otp:{$user->id}", Hash::make($validOtp), now()->addMinutes(5));
    Cache::put("phone_otp_number:{$user->id}", '081234567890', now()->addMinutes(5));

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('showOtpModal', true)
        ->set('phoneOtp', $validOtp)
        ->call('verifyPhoneOtp')
        ->assertSet('showOtpModal', false)
        ->assertSet('phoneOtp', '')
        ->assertDispatched('show-toast');

    $user->refresh();
    expect($user->phone_verified_at)->not->toBeNull()
        ->and($user->isPhoneVerified())->toBeTrue()
        ->and(Cache::has("phone_otp:{$user->id}"))->toBeFalse();
});

test('verification fails when incorrect otp code is entered', function () {
    $user = User::factory()->create([
        'phone_number' => '081234567890',
        'phone_verified_at' => null,
    ]);

    Cache::put("phone_otp:{$user->id}", Hash::make('123456'), now()->addMinutes(5));
    Cache::put("phone_otp_number:{$user->id}", '081234567890', now()->addMinutes(5));

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('showOtpModal', true)
        ->set('phoneOtp', '999999')
        ->call('verifyPhoneOtp')
        ->assertSet('showOtpModal', true);

    $user->refresh();
    expect($user->phone_verified_at)->toBeNull();
});

test('updating phone number resets verification status to unverified', function () {
    $user = User::factory()->create([
        'phone_number' => '081234567890',
        'phone_verified_at' => now(),
    ]);

    expect($user->isPhoneVerified())->toBeTrue();

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('phone_number', '089876543210')
        ->call('updateProfile')
        ->assertDispatched('show-toast');

    $user->refresh();
    expect($user->phone_number)->toBe('089876543210')
        ->and($user->phone_verified_at)->toBeNull()
        ->and($user->isPhoneVerified())->toBeFalse();
});
