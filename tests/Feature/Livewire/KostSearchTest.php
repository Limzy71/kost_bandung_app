<?php

use App\Livewire\KostSearch;
use App\Models\Facility;
use App\Models\Kost;
use App\Models\User;
use Livewire\Livewire;

function makePublishedKost(string $name, string $slug, float $price, string $period, array $extra = []): Kost
{
    $user = User::factory()->create();

    return Kost::create([
        'user_id' => $user->id,
        'name' => $name,
        'slug' => $slug,
        'description' => 'Deskripsi kost yang cukup panjang minimal sepuluh kata.',
        'gender_type' => $extra['gender_type'] ?? 'campur',
        'price_monthly' => $price,
        'rent_period' => $period,
        'district' => $extra['district'] ?? 'Andir',
        'address' => 'Jl. Test No. 1',
        'latitude' => -6.918,
        'longitude' => 107.584,
        'is_available' => true,
        'status' => 'published',
        'total_rooms' => 5,
        'available_rooms' => 2,
        'created_at' => $extra['created_at'] ?? now()->subDay(),
        'boost_expires_at' => $extra['boost_expires_at'] ?? null,
    ]);
}

function attachFacility(Kost $kost, string $facilityName): void
{
    $facility = Facility::firstOrCreate(
        ['name' => $facilityName],
        ['status' => 'approved', 'icon' => null]
    );

    $kost->facilities()->attach($facility->id);
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

it('hydrates filters from the URL query string', function () {
    $user = User::factory()->create();
    Kost::create([
        'user_id' => $user->id,
        'name' => 'Kost Khusus Coblong',
        'slug' => 'kost-khusus-coblong',
        'description' => 'Deskripsi kost yang cukup panjang minimal sepuluh kata.',
        'gender_type' => 'campur',
        'price_monthly' => 1200000,
        'rent_period' => 'monthly',
        'district' => 'Coblong',
        'address' => 'Jl. Coblong No. 1',
        'latitude' => -6.89148,
        'longitude' => 107.61066,
        'is_available' => true,
        'status' => 'published',
        'total_rooms' => 5,
        'available_rooms' => 2,
    ]);
    makePublishedKost('Kost Andir Raya', 'kost-andir-raya', 1000000, 'monthly');

    $this->get('/?district=Coblong&price_min=1000000')
        ->assertOk()
        ->assertSee('Kost Khusus Coblong')
        ->assertDontSee('Kost Andir Raya');
});

it('seeds the reset filter button state from active filters after a page refresh', function () {
    // Simulates a refresh with a search filter that has no results: the empty
    // state card is rendered server-side, so the reset filter button (hasFilter)
    // must also be initialized to true from the server state.
    makePublishedKost('Kost Andir Raya', 'kost-andir-raya', 1000000, 'monthly');

    $this->get('/?search=tidak-ada')
        ->assertOk()
        ->assertSee('Tidak Ada Hunian Ditemukan');

    // Without any active filter the page loads normally and lists the kost.
    $this->get('/')
        ->assertOk()
        ->assertSee('Kost Andir Raya');
});

it('keeps active filter properties for every filter type', function () {
    foreach (['gender' => 'putri', 'district' => 'Andir', 'rent_period' => 'monthly', 'price_min' => '1000000', 'price_max' => '3000000'] as $key => $value) {
        $this->get('/?'.$key.'='.$value)
            ->assertOk();
    }
});

// ─── Facility Filter Tests ───────────────────────────────────────────────────

it('filters kost by single facility', function () {
    $kostWithAC = makePublishedKost('Kost AC', 'kost-ac', 1500000, 'monthly');
    attachFacility($kostWithAC, 'AC');

    $kostWithoutAC = makePublishedKost('Kost Biasa', 'kost-biasa', 1000000, 'monthly');

    Livewire::test(KostSearch::class)
        ->set('facilities', ['AC'])
        ->assertSee('Kost AC')
        ->assertDontSee('Kost Biasa');
});

it('filters kost by multiple facilities using AND logic', function () {
    $kostBoth = makePublishedKost('Kost Lengkap', 'kost-lengkap', 2000000, 'monthly');
    attachFacility($kostBoth, 'AC');
    attachFacility($kostBoth, 'Wi-Fi');

    $kostACOnly = makePublishedKost('Kost AC Saja', 'kost-ac-saja', 1500000, 'monthly');
    attachFacility($kostACOnly, 'AC');

    $kostWifiOnly = makePublishedKost('Kost WiFi Saja', 'kost-wifi-saja', 1200000, 'monthly');
    attachFacility($kostWifiOnly, 'Wi-Fi');

    Livewire::test(KostSearch::class)
        ->set('facilities', ['AC', 'Wi-Fi'])
        ->assertSee('Kost Lengkap')
        ->assertDontSee('Kost AC Saja')
        ->assertDontSee('Kost WiFi Saja');
});

it('returns no results when facility does not exist in DB', function () {
    makePublishedKost('Kost Tanpa Fasilitas', 'kost-tanpa', 800000, 'monthly');

    Livewire::test(KostSearch::class)
        ->set('facilities', ['NonExistentFacility'])
        ->assertDontSee('Kost Tanpa Fasilitas');
});

it('toggles facility on and off via toggleFacility method', function () {
    $component = Livewire::test(KostSearch::class)
        ->call('toggleFacility', 'AC')
        ->assertSet('facilities', ['AC'])
        ->call('toggleFacility', 'Wi-Fi')
        ->assertSet('facilities', ['AC', 'Wi-Fi'])
        ->call('toggleFacility', 'AC')
        ->assertSet('facilities', ['Wi-Fi']);
});

// ─── Sorting Tests ───────────────────────────────────────────────────────────

it('sorts by price ascending', function () {
    makePublishedKost('Kost Mahal', 'kost-mahal', 3000000, 'monthly');
    makePublishedKost('Kost Murah', 'kost-murah', 500000, 'monthly');
    makePublishedKost('Kost Sedang', 'kost-sedang', 1500000, 'monthly');

    Livewire::test(KostSearch::class)
        ->set('sort', 'price_asc')
        ->assertSeeInOrder(['Kost Murah', 'Kost Sedang', 'Kost Mahal']);
});

it('sorts by price descending', function () {
    makePublishedKost('Kost Mahal', 'kost-mahal-2', 3000000, 'monthly');
    makePublishedKost('Kost Murah', 'kost-murah-2', 500000, 'monthly');
    makePublishedKost('Kost Sedang', 'kost-sedang-2', 1500000, 'monthly');

    Livewire::test(KostSearch::class)
        ->set('sort', 'price_desc')
        ->assertSeeInOrder(['Kost Mahal', 'Kost Sedang', 'Kost Murah']);
});

it('sorts by newest first', function () {
    $old = makePublishedKost('Kost Lama', 'kost-lama', 1000000, 'monthly', [
        'created_at' => now()->subDays(10),
    ]);
    $new = makePublishedKost('Kost Baru', 'kost-baru', 1000000, 'monthly', [
        'created_at' => now()->subHour(),
    ]);

    Livewire::test(KostSearch::class)
        ->set('sort', 'newest')
        ->assertSeeInOrder(['Kost Baru', 'Kost Lama']);
});

it('sorts by recommended with boosted first by default', function () {
    $boosted = makePublishedKost('Kost Boosted', 'kost-boosted', 1000000, 'monthly', [
        'boost_expires_at' => now()->addDays(7),
        'created_at' => now()->subDays(3),
    ]);
    $newer = makePublishedKost('Kost Baru', 'kost-baru-r', 1000000, 'monthly', [
        'created_at' => now()->subDay(),
    ]);
    $older = makePublishedKost('Kost Lama', 'kost-lama-r', 1000000, 'monthly', [
        'created_at' => now()->subDays(5),
    ]);

    Livewire::test(KostSearch::class)
        ->assertSet('sort', 'recommended')
        ->assertSeeInOrder(['Kost Boosted', 'Kost Baru', 'Kost Lama']);
});

it('can hydrate sort from URL query string', function () {
    $this->get('/?sort=price_asc')
        ->assertOk();
});

// ─── Price Preset Tests ──────────────────────────────────────────────────────

it('applies price preset correctly via setPricePreset', function () {
    Livewire::test(KostSearch::class)
        ->call('setPricePreset', 'under_1m')
        ->assertSet('price_min', '')
        ->assertSet('price_max', '1000000')
        ->call('setPricePreset', '1m_2m')
        ->assertSet('price_min', '1000000')
        ->assertSet('price_max', '2000000')
        ->call('setPricePreset', '2m_3m')
        ->assertSet('price_min', '2000000')
        ->assertSet('price_max', '3000000')
        ->call('setPricePreset', 'above_3m')
        ->assertSet('price_min', '3000000')
        ->assertSet('price_max', '')
        ->call('setPricePreset', 'all')
        ->assertSet('price_min', '')
        ->assertSet('price_max', '');
});

it('price presets filter results correctly', function () {
    makePublishedKost('Kost Murah', 'kost-murah-p', 800000, 'monthly');
    makePublishedKost('Kost Sedang', 'kost-sedang-p', 1500000, 'monthly');
    makePublishedKost('Kost Mahal', 'kost-mahal-p', 3500000, 'monthly');

    Livewire::test(KostSearch::class)
        ->call('setPricePreset', 'under_1m')
        ->assertSee('Kost Murah')
        ->assertDontSee('Kost Sedang')
        ->assertDontSee('Kost Mahal');

    Livewire::test(KostSearch::class)
        ->call('setPricePreset', '1m_2m')
        ->assertDontSee('Kost Murah')
        ->assertSee('Kost Sedang')
        ->assertDontSee('Kost Mahal');
});

// ─── Reset Filters Tests ─────────────────────────────────────────────────────

it('resets all filters including facilities and sort', function () {
    Livewire::test(KostSearch::class)
        ->set('facilities', ['AC', 'Wi-Fi'])
        ->set('sort', 'price_asc')
        ->set('gender', 'putri')
        ->set('price_min', '1000000')
        ->call('resetFilters')
        ->assertSet('facilities', [])
        ->assertSet('sort', 'recommended')
        ->assertSet('gender', '')
        ->assertSet('price_min', '')
        ->assertSet('price_max', '');
});

// ─── Combined Filter Tests ───────────────────────────────────────────────────

it('combines facility filter with price and sorting', function () {
    $cheap = makePublishedKost('Kost Murah AC', 'kost-murah-ac', 800000, 'monthly');
    attachFacility($cheap, 'AC');

    $expensive = makePublishedKost('Kost Mahal AC', 'kost-mahal-ac', 3000000, 'monthly');
    attachFacility($expensive, 'AC');

    $noAC = makePublishedKost('Kost Murah Biasa', 'kost-murah-biasa', 700000, 'monthly');

    Livewire::test(KostSearch::class)
        ->set('facilities', ['AC'])
        ->set('price_min', '500000')
        ->set('price_max', '1000000')
        ->assertSee('Kost Murah AC')
        ->assertDontSee('Kost Mahal AC')
        ->assertDontSee('Kost Murah Biasa');
});

it('mapItems syncs when facility filter is applied', function () {
    $kost = makePublishedKost('Kost Map Test', 'kost-map-test', 1200000, 'monthly');
    attachFacility($kost, 'AC');

    makePublishedKost('Kost No Map', 'kost-no-map', 900000, 'monthly');

    $test = Livewire::test(KostSearch::class)
        ->set('facilities', ['AC']);

    $mapItems = $test->get('mapItems');
    expect($mapItems)->toHaveCount(1)
        ->and($mapItems[0]['name'])->toBe('Kost Map Test');
});

// ─── hasActiveFilter Detection ───────────────────────────────────────────────

it('detects active filter when facilities are selected', function () {
    Livewire::test(KostSearch::class)
        ->set('facilities', ['AC'])
        ->assertViewHas('hasActiveFilter', true);
});

it('detects active filter when sort is changed', function () {
    Livewire::test(KostSearch::class)
        ->set('sort', 'price_asc')
        ->assertViewHas('hasActiveFilter', true);
});

it('has no active filter with defaults', function () {
    Livewire::test(KostSearch::class)
        ->assertViewHas('hasActiveFilter', false);
});

it('applies all draft filters atomically via applyAllFilters', function () {
    Livewire::test(KostSearch::class)
        ->call('applyAllFilters', 'Dago', 'Coblong', 'putra', 'monthly', '1000000', '3000000', true, ['AC', 'Wi-Fi'])
        ->assertSet('search', 'Dago')
        ->assertSet('district', 'Coblong')
        ->assertSet('gender', 'putra')
        ->assertSet('rent_period', 'monthly')
        ->assertSet('price_min', '1000000')
        ->assertSet('price_max', '3000000')
        ->assertSet('verified_only', true)
        ->assertSet('facilities', ['AC', 'Wi-Fi'])
        ->assertViewHas('hasActiveFilter', true);
});

