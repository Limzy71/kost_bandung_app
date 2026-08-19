<?php

use App\Models\User;

test('guest cannot access admin whatsapp qr pairing page', function () {
    $response = $this->get(route('admin.whatsapp.qr'));
    $response->assertRedirect(route('login'));
});

test('regular user cannot access admin whatsapp qr pairing page', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
        'terms_accepted_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('admin.whatsapp.qr'));
    $response->assertForbidden();
});

test('owner cannot access admin whatsapp qr pairing page', function () {
    $owner = User::factory()->create([
        'role' => 'owner',
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
        'terms_accepted_at' => now(),
    ]);

    $response = $this->actingAs($owner)->get(route('admin.whatsapp.qr'));
    $response->assertForbidden();
});

test('admin can access admin whatsapp qr pairing page', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
        'terms_accepted_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.whatsapp.qr'));
    $response->assertOk()
        ->assertSee('Scan WhatsApp Gateway')
        ->assertSee('Khusus Administrator');
});
