<?php

use App\Livewire\Profile\Security;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Features;
use Livewire\Livewire;

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
