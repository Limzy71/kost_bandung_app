<?php

use App\Livewire\Settings\Profile;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create(['email_verified_at' => now()]));

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('sends a verification notification when the email is changed', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Notification::fake();

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->fresh()->email_verified_at)->toBeNull();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('prevents an admin from changing their email', function () {
    $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    Notification::fake();

    $this->actingAs($admin);

    Livewire::test(Profile::class)
        ->set('name', 'Test Admin')
        ->set('email', 'admin-hacked@example.com')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($admin->fresh()->email)->toBe($admin->email);
    expect($admin->fresh()->name)->toEqual('Test Admin');

    Notification::assertNothingSent();
});

test('user can delete their account', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});

test('rejects a name that is too short', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('name', 'a')
        ->call('updateProfileInformation')
        ->assertHasErrors(['name' => 'min']);

    expect($user->fresh()->name)->not->toBe('a');
});

test('rejects a name containing invalid characters', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('name', 'ldhjdkhsdhgz;j')
        ->call('updateProfileInformation')
        ->assertHasErrors(['name' => 'regex']);

    expect($user->fresh()->name)->not->toBe('ldhjdkhsdhgz;j');
});

test('accepts a valid unicode name', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('name', 'Agus Setiawan')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->fresh()->name)->toBe('Agus Setiawan');
});
