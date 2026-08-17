<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

test('google oauth redirect route redirects to provider', function () {
    $response = $this->get(route('auth.google.redirect'));

    $response->assertRedirect();
});

test('google oauth callback registers a new user and logs them in', function () {
    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('google-123456');
    $abstractUser->shouldReceive('getName')->andReturn('Budi Santoso');
    $abstractUser->shouldReceive('getNickname')->andReturn('budi');
    $abstractUser->shouldReceive('getEmail')->andReturn('budi@example.com');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a/avatar123');

    Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('onboarding'));
    $this->assertAuthenticated();

    $user = User::where('email', 'budi@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Budi Santoso')
        ->and($user->google_id)->toBe('google-123456')
        ->and($user->avatar)->toBe('https://lh3.googleusercontent.com/a/avatar123')
        ->and($user->avatar_url)->toBe('https://lh3.googleusercontent.com/a/avatar123')
        ->and($user->role)->toBeNull()
        ->and($user->terms_accepted_at)->toBeNull()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->password)->toBeNull();
});

test('google oauth callback logs in existing user and links google id', function () {
    $existingUser = User::factory()->create([
        'name' => 'Siti Nurhaliza',
        'email' => 'siti@example.com',
        'role' => 'user',
        'google_id' => null,
    ]);

    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('google-987654');
    $abstractUser->shouldReceive('getName')->andReturn('Siti Nurhaliza');
    $abstractUser->shouldReceive('getEmail')->andReturn('siti@example.com');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a/avatar456');

    Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($existingUser);

    $existingUser->refresh();
    expect($existingUser->google_id)->toBe('google-987654')
        ->and($existingUser->avatar)->toBe('https://lh3.googleusercontent.com/a/avatar456')
        ->and($existingUser->email_verified_at)->not->toBeNull();
});

test('google oauth callback redirects owner to dashboard', function () {
    $owner = User::factory()->create([
        'email' => 'owner@example.com',
        'role' => 'owner',
        'google_id' => 'google-owner-111',
    ]);

    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('google-owner-111');
    $abstractUser->shouldReceive('getEmail')->andReturn('owner@example.com');
    $abstractUser->shouldReceive('getAvatar')->andReturn(null);

    Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($owner);
});

test('google oauth callback handles exceptions gracefully and redirects to login', function () {
    Socialite::shouldReceive('driver->user')->andThrow(new Exception('Invalid state or consent cancelled'));

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});
