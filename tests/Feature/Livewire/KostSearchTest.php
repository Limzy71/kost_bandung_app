<?php

use App\Livewire\KostSearch;
use App\Models\Kost;
use Livewire\Livewire;

function makePublishedKost(string $name, string $slug, float $price, string $period): Kost
{
    $user = \App\Models\User::factory()->create();

    return Kost::create([
        'user_id' => $user->id,
        'name' => $name,
        'slug' => $slug,
        'description' => 'Deskripsi kost yang cukup panjang minimal sepuluh kata.',
        'gender_type' => 'campur',
        'price_monthly' => $price,
        'rent_period' => $period,
        'district' => 'Andir',
        'address' => 'Jl. Test No. 1',
        'latitude' => -6.918,
        'longitude' => 107.584,
        'is_available' => true,
        'status' => 'published',
        'total_rooms' => 5,
        'available_rooms' => 2,
    ]);
}

it('filters price by monthly-equivalent across primary rent periods', function () {
    makePublishedKost('Kost Bulanan', 'kost-bulanan', 1000000, 'monthly');
    makePublishedKost('Kost Tahunan', 'kost-tahunan', 12000000, 'yearly');
    makePublishedKost('Kost Harian', 'kost-harian', 200000, 'daily');

    Livewire::test(KostSearch::class)
        ->set('price_min', 900000)
        ->set('price_max', 1100000)
        ->assertSee('Kost Bulanan')
        ->assertSee('Kost Tahunan')
        ->assertDontSee('Kost Harian');
});

it('shows the primary rent period unit on the search card', function () {
    makePublishedKost('Kost Tahunan', 'kost-tahunan-2', 12000000, 'yearly');
    makePublishedKost('Kost Bulanan', 'kost-bulanan-2', 1000000, 'monthly');

    Livewire::test(KostSearch::class)
        ->assertSee('Rp 12.000.000')
        ->assertSee('/tahun')
        ->assertSee('/bln');
});
