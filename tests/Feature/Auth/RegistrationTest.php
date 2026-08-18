<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Livewire\Livewire;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSeeLivewire(Register::class);
});

test('new seeker (user) can register manually and redirects to verification hub', function () {
    Livewire::test(Register::class)
        ->set('name', 'Budi Santoso')
        ->set('email', 'budi@example.com')
        ->set('phone_number', '081234567891')
        ->set('password', 'Rahasia123!')
        ->set('password_confirmation', 'Rahasia123!')
        ->set('role', 'user')
        ->set('terms', true)
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'budi@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('user')
        ->and($user->phone_number)->toBe('081234567891')
        ->and($user->terms_accepted_at)->not->toBeNull()
        ->and($user->hasCompletedOnboarding())->toBeTrue();

    $this->assertAuthenticatedAs($user);
});

test('new owner can register manually with business name and redirects to verification hub', function () {
    Livewire::test(Register::class)
        ->set('name', 'Haji Sulaeman')
        ->set('email', 'sulaeman@example.com')
        ->set('phone_number', '081298765432')
        ->set('password', 'Rahasia123!')
        ->set('password_confirmation', 'Rahasia123!')
        ->set('role', 'owner')
        ->set('business_name', 'Kost Berkah Dago')
        ->set('terms', true)
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'sulaeman@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('owner')
        ->and($user->business_name)->toBe('Kost Berkah Dago')
        ->and($user->phone_number)->toBe('081298765432')
        ->and($user->terms_accepted_at)->not->toBeNull()
        ->and($user->hasCompletedOnboarding())->toBeTrue();

    $this->assertAuthenticatedAs($user);
});

test('owner registration requires business name', function () {
    Livewire::test(Register::class)
        ->set('name', 'Haji Sulaeman')
        ->set('email', 'sulaeman2@example.com')
        ->set('phone_number', '081298765433')
        ->set('password', 'Rahasia123!')
        ->set('password_confirmation', 'Rahasia123!')
        ->set('role', 'owner')
        ->set('business_name', '')
        ->set('terms', true)
        ->call('register')
        ->assertHasErrors(['business_name']);
});

test('registration validates phone number regex and uniqueness', function () {
    User::factory()->create([
        'phone_number' => '081234567890',
        'email' => 'existing@example.com',
    ]);

    // Invalid format
    Livewire::test(Register::class)
        ->set('phone_number', '12345')
        ->call('register')
        ->assertHasErrors(['phone_number']);

    // Duplicate phone
    Livewire::test(Register::class)
        ->set('phone_number', '081234567890')
        ->call('register')
        ->assertHasErrors(['phone_number']);
});
