<?php

use App\Models\User;

it('shows a public profile for an owner', function () {
    $owner = User::factory()->create([
        'role' => 'owner',
        'business_name' => 'Kost Sejahtera',
        'phone_number' => '081234567890',
    ]);
    $kost = profileTestKost($owner, 'published', 'Kost Andir Premium');

    $this->get(route('profile.owner', $owner))
        ->assertOk()
        ->assertSee('Kost Sejahtera')
        ->assertSee('081234567890')
        ->assertSee('Kost Andir Premium');
});

it('only shows published kosts on the public owner profile', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $published = profileTestKost($owner, 'published', 'Kost Tayang');
    $pending = profileTestKost($owner, 'pending', 'Kost Menunggu');
    $rejected = profileTestKost($owner, 'rejected', 'Kost Ditolak');

    $this->get(route('profile.owner', $owner))
        ->assertOk()
        ->assertSee($published->name)
        ->assertDontSee($pending->name)
        ->assertDontSee($rejected->name);
});

it('returns 404 when the public profile belongs to a non-owner', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->get(route('profile.owner', $user))->assertNotFound();
});

it('does not expose the owner private phone number to guests when not set', function () {
    $owner = User::factory()->create([
        'role' => 'owner',
        'business_name' => 'Kost Rapi',
        'phone_number' => null,
    ]);

    $this->get(route('profile.owner', $owner))
        ->assertOk()
        ->assertSee('Kost Rapi');
});
