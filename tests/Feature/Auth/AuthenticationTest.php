<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSeeLivewire(\App\Livewire\Auth\Login::class);
});

test('user can authenticate via Livewire login and redirects to home', function () {
    $user = User::factory()->create([
        'email' => 'userlogin@example.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'role' => 'user',
        'terms_accepted_at' => now(),
    ]);

    \Livewire\Livewire::test(\App\Livewire\Auth\Login::class)
        ->set('email', 'userlogin@example.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

test('owner can authenticate via Livewire login and redirects to dashboard', function () {
    $owner = User::factory()->create([
        'email' => 'ownerlogin@example.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'role' => 'owner',
        'terms_accepted_at' => now(),
    ]);

    \Livewire\Livewire::test(\App\Livewire\Auth\Login::class)
        ->set('email', 'ownerlogin@example.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($owner);
});

test('users can not authenticate with invalid password in Livewire login', function () {
    $user = User::factory()->create([
        'email' => 'invalidpass@example.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
    ]);

    \Livewire\Livewire::test(\App\Livewire\Auth\Login::class)
        ->set('email', 'invalidpass@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});
