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

it('filters price by monthly-equivalent across primary rent periods', function () {
    makePublishedKost('Kost Bulanan', 'kost-bulanan', 1000000, 'monthly');
    makePublishedKost('Kost Tahunan', 'kost-tahunan', 12000000, 'yearly');
    makePublishedKost('Kost 3 Bulanan', 'kost-3-bulanan', 3000000, 'three_monthly');
    makePublishedKost('Kost 6 Bulanan', 'kost-6-bulanan', 6000000, 'six_monthly');
    makePublishedKost('Kost Harian Murah', 'kost-harian-murah', 30000, 'daily');
    makePublishedKost('Kost Mingguan', 'kost-mingguan', 230770, 'weekly');
    makePublishedKost('Kost Harian Mahal', 'kost-harian-mahal', 200000, 'daily');

    Livewire::test(KostSearch::class)
        ->set('price_min', 900000)
        ->set('price_max', 1100000)
        ->assertSee('Kost Bulanan')
        ->assertSee('Kost Tahunan')
        ->assertSee('Kost 3 Bulanan')
        ->assertSee('Kost 6 Bulanan')
        ->assertSee('Kost Harian Murah')
        ->assertSee('Kost Mingguan')
        ->assertDontSee('Kost Harian Mahal');
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
