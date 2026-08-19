<?php

use App\Models\User;

test('unverified phone user accessing protected route is redirected to verify account', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => null,
        'role' => 'owner',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('verify.account'));
});

test('unverified email user accessing protected route is redirected to verify account', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
        'phone_verified_at' => now(),
        'role' => 'user',
    ]);

    $response = $this->actingAs($user)->get(route('profile.show'));

    $response->assertRedirect(route('verify.account'));
});

test('fully verified user can access protected route', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
        'role' => 'owner',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});
