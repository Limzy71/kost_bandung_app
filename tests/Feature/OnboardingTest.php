<?php

use App\Livewire\Onboarding\CompleteOnboarding;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Livewire\Livewire;

it('creates new Google user with null role and redirects to onboarding', function () {
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn('google-new-999');
    $socialiteUser->shouldReceive('getEmail')->andReturn('newuser@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('New Google User');
    $socialiteUser->shouldReceive('getNickname')->andReturn(null);
    $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a/new');

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('onboarding'));

    $user = User::where('email', 'newuser@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->role)->toBeNull()
        ->and($user->terms_accepted_at)->toBeNull()
        ->and($user->hasCompletedOnboarding())->toBeFalse();

    $this->assertAuthenticatedAs($user);
});

it('redirects existing completed user directly to their destination on Google login', function () {
    $existingOwner = User::factory()->create([
        'email' => 'owner@example.com',
        'role' => 'owner',
        'terms_accepted_at' => now(),
        'google_id' => null,
    ]);

    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn('google-owner-123');
    $socialiteUser->shouldReceive('getEmail')->andReturn('owner@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('Existing Owner');
    $socialiteUser->shouldReceive('getNickname')->andReturn(null);
    $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('dashboard'));

    expect($existingOwner->fresh()->google_id)->toBe('google-owner-123');
});

it('renders onboarding page for user with incomplete onboarding', function () {
    $user = User::factory()->create([
        'role' => null,
        'terms_accepted_at' => null,
    ]);

    $this->actingAs($user)
        ->get(route('onboarding'))
        ->assertOk()
        ->assertSee('Pilih Tipe Akun');
});

it('redirects completed user away from onboarding page', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'terms_accepted_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('onboarding'))
        ->assertRedirect(route('home'));
});

it('redirects incomplete user away from protected routes to onboarding', function () {
    $incompleteUser = User::factory()->create([
        'role' => null,
        'terms_accepted_at' => null,
    ]);

    $this->actingAs($incompleteUser)
        ->get(route('profile.show'))
        ->assertRedirect(route('onboarding'));
});

it('allows incomplete user to complete onboarding as a seeker', function () {
    $user = User::factory()->create([
        'role' => null,
        'terms_accepted_at' => null,
    ]);

    Livewire::actingAs($user)
        ->test(CompleteOnboarding::class)
        ->set('role', 'user')
        ->set('terms', true)
        ->call('complete')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $user->refresh();
    expect($user->role)->toBe('user')
        ->and($user->terms_accepted_at)->not->toBeNull()
        ->and($user->hasCompletedOnboarding())->toBeTrue();
});

it('allows incomplete user to complete onboarding as an owner with valid details', function () {
    $user = User::factory()->create([
        'role' => null,
        'terms_accepted_at' => null,
    ]);

    Livewire::actingAs($user)
        ->test(CompleteOnboarding::class)
        ->set('role', 'owner')
        ->set('business_name', 'Kost Melati Dago')
        ->set('phone_number', '081299988877')
        ->set('terms', true)
        ->call('complete')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->role)->toBe('owner')
        ->and($user->business_name)->toBe('Kost Melati Dago')
        ->and($user->phone_number)->toBe('081299988877')
        ->and($user->terms_accepted_at)->not->toBeNull()
        ->and($user->hasCompletedOnboarding())->toBeTrue();
});

it('validates owner onboarding requires business_name and valid unique phone_number', function () {
    $existingOwner = User::factory()->create([
        'phone_number' => '081234567890',
        'role' => 'owner',
    ]);

    $user = User::factory()->create([
        'role' => null,
        'terms_accepted_at' => null,
    ]);

    Livewire::actingAs($user)
        ->test(CompleteOnboarding::class)
        ->set('role', 'owner')
        ->set('business_name', '')
        ->set('phone_number', 'invalid-phone')
        ->set('terms', true)
        ->call('complete')
        ->assertHasErrors(['business_name', 'phone_number']);

    Livewire::actingAs($user)
        ->test(CompleteOnboarding::class)
        ->set('role', 'owner')
        ->set('business_name', 'Kost Baru')
        ->set('phone_number', '081234567890') // duplicate phone
        ->set('terms', true)
        ->call('complete')
        ->assertHasErrors(['phone_number']);
});

it('validates terms must be accepted on onboarding', function () {
    $user = User::factory()->create([
        'role' => null,
        'terms_accepted_at' => null,
    ]);

    Livewire::actingAs($user)
        ->test(CompleteOnboarding::class)
        ->set('role', 'user')
        ->set('terms', false)
        ->call('complete')
        ->assertHasErrors(['terms']);
});

it('allows incomplete user to logout safely from onboarding', function () {
    $user = User::factory()->create([
        'role' => null,
        'terms_accepted_at' => null,
    ]);

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect('/');

    $this->assertGuest();
});
