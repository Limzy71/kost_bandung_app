<?php

use App\Livewire\KostSearch;
use App\Models\Kost;
use App\Models\User;
use Livewire\Livewire;

function makePublishedKost(string $name, string $slug, float $price, string $period): Kost
{
    $user = User::factory()->create();

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

it('filters price by nominal amount per rent period', function () {
    makePublishedKost('Kost Bulanan Murah', 'kost-bulanan-murah', 900000, 'monthly');
    makePublishedKost('Kost Bulanan Mahal', 'kost-bulanan-mahal', 3000000, 'monthly');
    makePublishedKost('Kost Tahunan', 'kost-tahunan', 10000000, 'yearly');
    makePublishedKost('Kost Harian', 'kost-harian', 100000, 'daily');

    // Test nominal price filter without rent period (searches all periods by nominal amount)
    Livewire::test(KostSearch::class)
        ->set('price_min', 800000)
        ->set('price_max', 2000000)
        ->assertSee('Kost Bulanan Murah') // 900k is within 800k - 2M
        ->assertDontSee('Kost Bulanan Mahal') // 3M > 2M
        ->assertDontSee('Kost Tahunan') // 10M > 2M
        ->assertDontSee('Kost Harian'); // 100k < 800k

    // Test rent period filter
    Livewire::test(KostSearch::class)
        ->set('rent_period', 'yearly')
        ->set('price_min', 5000000)
        ->set('price_max', 15000000)
        ->assertSee('Kost Tahunan')
        ->assertDontSee('Kost Bulanan Murah');
});

it('shows the actual rent period price on the search card and map', function () {
    makePublishedKost('Kost Tahunan', 'kost-tahunan-2', 12000000, 'yearly');
    makePublishedKost('Kost Bulanan', 'kost-bulanan-2', 1000000, 'monthly');

    $test = Livewire::test(KostSearch::class);

    $test->assertSee('Rp 1.000.000')
        ->assertSee('/bln')
        ->assertSee('Rp 12.000.000')
        ->assertSee('/tahun');

    $units = collect($test->get('mapItems'))->pluck('price_unit')->sort()->values()->all();
    expect($units)->toBe(['/bln', '/tahun']);
});
