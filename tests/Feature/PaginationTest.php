<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\OwnerDashboard;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function createTestKosts(User $user, int $count = 25): void
{
    for ($i = 1; $i <= $count; $i++) {
        Kost::create([
            'user_id' => $user->id,
            'name' => "Kost Dago #{$i}",
            'slug' => "kost-dago-{$i}",
            'description' => 'Deskripsi kost',
            'gender_type' => 'putri',
            'price_monthly' => 1500000,
            'address' => 'Jl. Dago No. '.$i,
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

test('hard refresh with page parameter keeps other filters for kost search', function () {
    $user = User::factory()->create(['role' => 'owner']);
    createTestKosts($user, 25);

    $response = $this->get('/?search=dago&district=Coblong&page=2');
    $response->assertRedirect('http://localhost/?search=dago&district=Coblong');
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

test('resetting search from empty result state on page 2 returns to page 1 with results', function () {
    $user = User::factory()->create(['role' => 'owner']);
    createTestKosts($user, 25);

    Livewire::actingAs($user)
        ->test(OwnerDashboard::class)
        ->set('search', 'no-such-kost')
        ->call('setPage', 2)
        ->assertViewHas('kosts', fn ($kosts) => $kosts->isEmpty())
        ->call('resetSearch')
        ->assertSet('search', '')
        ->assertViewHas('kosts', fn ($kosts) => $kosts->currentPage() === 1 && $kosts->isNotEmpty());
});

test('toggling availability preserves available_rooms', function () {
    $user = User::factory()->create(['role' => 'owner']);
    $kost = Kost::create([
        'user_id' => $user->id,
        'name' => 'Kost Dago #1',
        'slug' => 'kost-dago-1',
        'description' => 'Deskripsi kost',
        'gender_type' => 'putri',
        'price_monthly' => 1500000,
        'address' => 'Jl. Dago No. 1',
        'district' => 'Coblong',
        'latitude' => -6.89148,
        'longitude' => 107.61066,
        'is_available' => true,
        'total_rooms' => 5,
        'available_rooms' => 3,
    ]);

    Livewire::actingAs($user)
        ->test(OwnerDashboard::class)
        ->call('toggleAvailability', $kost->id)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('kosts', [
        'id' => $kost->id,
        'is_available' => false,
        'available_rooms' => 3,
    ]);

    Livewire::actingAs($user)
        ->test(OwnerDashboard::class)
        ->call('toggleAvailability', $kost->id)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('kosts', [
        'id' => $kost->id,
        'is_available' => true,
        'available_rooms' => 3,
    ]);
});
