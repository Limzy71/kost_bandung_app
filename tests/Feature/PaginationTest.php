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

test('scenario 1 & 2: owner dashboard page parameter persists on mount and reload without redirect', function () {
    $user = User::factory()->create(['role' => 'owner']);
    createTestKosts($user, 25);

    $response = $this->actingAs($user)->get('/dashboard?page=2');
    $response->assertStatus(200);
    $response->assertDontSee('Redirecting to');

    Livewire::actingAs($user)
        ->withQueryParams(['page' => 2])
        ->test(OwnerDashboard::class)
        ->assertViewHas('kosts', fn ($kosts) => $kosts->currentPage() === 2);
});

test('scenario 3: typing search in owner dashboard resets page to 1', function () {
    $user = User::factory()->create(['role' => 'owner']);
    createTestKosts($user, 25);

    Livewire::actingAs($user)
        ->withQueryParams(['page' => 2])
        ->test(OwnerDashboard::class)
        ->assertViewHas('kosts', fn ($kosts) => $kosts->currentPage() === 2)
        ->set('search', 'dago')
        ->assertViewHas('kosts', fn ($kosts) => $kosts->currentPage() === 1);
});

test('scenario 4: resetting search in owner dashboard clears search input and resets page to 1', function () {
    $user = User::factory()->create(['role' => 'owner']);
    createTestKosts($user, 25);

    Livewire::actingAs($user)
        ->withQueryParams(['page' => 2, 'search' => 'dago'])
        ->test(OwnerDashboard::class)
        ->call('resetSearch')
        ->assertSet('search', '')
        ->assertViewHas('kosts', fn ($kosts) => $kosts->currentPage() === 1);
});

test('scenario 5: kost search page parameter and filters persist on reload without redirect', function () {
    $user = User::factory()->create(['role' => 'owner']);
    createTestKosts($user, 25);

    $response = $this->get('/?page=2&gender=putri');
    $response->assertStatus(200);
    $response->assertDontSee('Redirecting to');

    Livewire::withQueryParams(['page' => 2, 'gender' => 'putri'])
        ->test(KostSearch::class)
        ->assertSet('gender', 'putri')
        ->assertViewHas('kosts', fn ($kosts) => $kosts->currentPage() === 2);
});

test('scenario 6: invalid page number page=999 does not crash and handles empty results gracefully', function () {
    $user = User::factory()->create(['role' => 'owner']);
    createTestKosts($user, 5);

    $response = $this->actingAs($user)->get('/dashboard?page=999');
    $response->assertStatus(200);

    $responseHome = $this->get('/?page=999');
    $responseHome->assertStatus(200);

    Livewire::actingAs($user)
        ->withQueryParams(['page' => 999])
        ->test(OwnerDashboard::class)
        ->assertStatus(200)
        ->assertViewHas('kosts', fn ($kosts) => $kosts->currentPage() === 999 && $kosts->isEmpty());

    Livewire::withQueryParams(['page' => 999])
        ->test(KostSearch::class)
        ->assertStatus(200)
        ->assertViewHas('kosts', fn ($kosts) => $kosts->currentPage() === 999 && $kosts->isEmpty());
});
