<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kost;
use App\Livewire\Dashboard\OwnerDashboard;
use App\Livewire\KostSearch;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createTestKosts(User $user, int $count = 25): void {
    for ($i = 1; $i <= $count; $i++) {
        Kost::create([
            'user_id' => $user->id,
            'name' => "Kost Dago #{$i}",
            'slug' => "kost-dago-{$i}",
            'description' => 'Deskripsi kost',
            'gender_type' => 'putri',
            'price_monthly' => 1500000,
            'address' => 'Jl. Dago No. ' . $i,
            'district' => 'Coblong',
            'latitude' => -6.89148,
            'longitude' => 107.61066,
            'is_available' => true,
        ]);
    }
}

test('hard refresh with page parameter redirects to base URL for owner dashboard', function () {
    $user = User::factory()->create(['role' => 'owner']);
    createTestKosts($user, 25);

    $response = $this->actingAs($user)->get('/dashboard?page=2');
    $response->assertRedirect('http://localhost/dashboard');
});

test('hard refresh with page parameter redirects to base URL for kost search', function () {
    $user = User::factory()->create(['role' => 'owner']);
    createTestKosts($user, 25);

    $response = $this->get('/?page=2');
    $response->assertRedirect('http://localhost');
});

test('typing search in owner dashboard resets page to 1', function () {
    $user = User::factory()->create(['role' => 'owner']);
    createTestKosts($user, 25);

    Livewire::actingAs($user)
        ->test(OwnerDashboard::class)
        ->set('search', 'dago')
        ->assertViewHas('kosts', fn ($kosts) => $kosts->currentPage() === 1);
});

test('resetting search in owner dashboard clears search input and resets page to 1', function () {
    $user = User::factory()->create(['role' => 'owner']);
    createTestKosts($user, 25);

    Livewire::actingAs($user)
        ->test(OwnerDashboard::class)
        ->set('search', 'dago')
        ->call('resetSearch')
        ->assertSet('search', '')
        ->assertViewHas('kosts', fn ($kosts) => $kosts->currentPage() === 1);
});
