<?php

use App\Livewire\Profile\Security;
use App\Livewire\Profile\TwoFactor\RecoveryCodes;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Features;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);
    Features::passkeys([
        'confirmPassword' => true,
    ]);
});

test('profile page renders security section', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/profil');

    $response->assertOk();

    $response->assertSee('Passkeys');
    $response->assertSee('Belum ada Passkey');
    $response->assertSee('Autentikasi Dua Faktor');
    $response->assertSee('Aktifkan 2FA');
    $response->assertSee('Ubah Kata Sandi');
});

test('sensitive action requires password confirmation when not confirmed', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    $component = Livewire::test(Security::class)
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $component->assertRedirect(route('password.confirm'));
});

test('sensitive action stores intended profile url before redirecting to password confirmation', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    $component = Livewire::test(Security::class)
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $component->assertRedirect(route('password.confirm'));

    expect(session('url.intended'))->toBe(route('profile.show'));
});

test('security section renders without two factor when feature is disabled', function () {
    config(['fortify.features' => []]);

    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/profil')
        ->assertOk()
        ->assertSee('Ubah Kata Sandi')
        ->assertDontSee('Autentikasi Dua Faktor');
});

test('two factor authentication disabled when confirmation abandoned between requests', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $user->forceFill([
        'two_factor_secret' => encrypt('test-secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => null,
    ])->save();

    $this->actingAs($user);

    $component = Livewire::test(Security::class);

    $component->assertSet('twoFactorEnabled', false);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
    ]);
});

test('password can be updated when password confirmed', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()]);

    $response = Livewire::test(Security::class)
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasNoErrors();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()]);

    $response = Livewire::test(Security::class)
        ->set('current_password', 'wrong-password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasErrors(['current_password']);
});

test('two factor is not enabled until a valid otp code is confirmed', function () {
    $secret = app(Google2FA::class)->generateSecretKey();

    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->forceFill([
        'two_factor_secret' => encrypt($secret),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => null,
    ])->save();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()]);

    $component = Livewire::test(Security::class);

    $component->assertSet('twoFactorEnabled', false)
        ->call('enable')
        ->assertSet('showModal', true)
        ->call('showVerificationIfNecessary')
        ->assertSet('showVerificationStep', true)
        ->assertSet('showRecoveryStep', false);
});

test('wrong otp code shows indonesian error and keeps modal in verification step', function () {
    $secret = app(Google2FA::class)->generateSecretKey();

    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->forceFill([
        'two_factor_secret' => encrypt($secret),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => null,
    ])->save();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()]);

    $component = Livewire::test(Security::class)
        ->call('enable')
        ->call('showVerificationIfNecessary')
        ->set('code', '000000')
        ->call('confirmTwoFactor');

    $component->assertHasErrors(['code'])
        ->assertSet('showVerificationStep', true)
        ->assertSet('showRecoveryStep', false)
        ->assertSet('twoFactorEnabled', false)
        ->assertSee('Kode 2FA yang Anda masukkan salah');

    expect($user->refresh()->two_factor_confirmed_at)->toBeNull();
});

test('correct otp code enables two factor and reveals recovery codes in the modal', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()]);

    $component = Livewire::test(Security::class)->call('enable');

    $secret = decrypt($user->refresh()->two_factor_secret);

    $component->call('showVerificationIfNecessary')
        ->set('code', app(Google2FA::class)->getCurrentOtp($secret))
        ->call('confirmTwoFactor');

    $component->assertHasNoErrors()
        ->assertSet('twoFactorEnabled', true)
        ->assertSet('showVerificationStep', false)
        ->assertSet('showRecoveryStep', true)
        ->assertCount('recoveryCodes', 8);

    $this->assertCount(8, json_decode(decrypt($user->refresh()->two_factor_recovery_codes), true));
    $this->assertNotNull($user->refresh()->two_factor_confirmed_at);
});

test('two factor secret and recovery codes are stored encrypted', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()]);

    Livewire::test(Security::class)->call('enable');

    $fresh = $user->refresh();

    expect($fresh->two_factor_secret)->not->toBeNull();
    expect($fresh->two_factor_recovery_codes)->not->toBeNull();

    $plainSecret = decrypt($fresh->two_factor_secret);
    $plainCodes = decrypt($fresh->two_factor_recovery_codes);

    expect($fresh->two_factor_secret)->not->toContain($plainSecret);
    expect($fresh->two_factor_recovery_codes)->not->toContain($plainCodes);
    expect(is_array(json_decode($plainCodes, true)))->toBeTrue();
});

test('disabling two factor requires password confirmation', function () {
    $secret = app(Google2FA::class)->generateSecretKey();

    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->forceFill([
        'two_factor_secret' => encrypt($secret),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $this->actingAs($user);

    $component = Livewire::test(Security::class)->call('disable');

    $component->assertRedirect(route('password.confirm'));
    $this->assertNotNull($user->refresh()->two_factor_confirmed_at);
});

test('regenerating recovery codes requires password confirmation and stores intended url', function () {
    $secret = app(Google2FA::class)->generateSecretKey();

    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->forceFill([
        'two_factor_secret' => encrypt($secret),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $this->actingAs($user);

    $component = Livewire::test(RecoveryCodes::class)->call('regenerateRecoveryCodes');

    $component->assertRedirect(route('password.confirm'));
    expect(session('url.intended'))->toBe(route('profile.show'));
});

test('oauth user without password can set a password directly', function () {
    $oauthUser = User::factory()->create([
        'email_verified_at' => now(),
        'password' => null,
        'google_id' => 'google-test-999',
    ]);

    Livewire::actingAs($oauthUser)
        ->test(Security::class)
        ->set('password', 'new-password-123')
        ->set('password_confirmation', 'new-password-123')
        ->call('updatePassword')
        ->assertHasNoErrors()
        ->assertDispatched('show-toast', message: 'Kata sandi berhasil dibuat. Anda sekarang bisa login dengan email dan kata sandi.');

    expect(Hash::check('new-password-123', $oauthUser->fresh()->password))->toBeTrue();
});

test('profile page shows create password heading for oauth user without password', function () {
    $oauthUser = User::factory()->create([
        'email_verified_at' => now(),
        'password' => null,
        'google_id' => 'google-test-999',
    ]);

    $this->actingAs($oauthUser)
        ->get('/profil')
        ->assertOk()
        ->assertSee('Buat Kata Sandi')
        ->assertSee('Anda masuk dengan akun Google');
});
