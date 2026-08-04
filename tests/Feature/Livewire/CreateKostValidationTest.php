<?php

use App\Livewire\Dashboard\CreateKost;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

it('fails validation when available_rooms is greater than total_rooms', function () {
    Livewire::test(CreateKost::class)
        ->set('total_rooms', 10)
        ->set('available_rooms', 15)
        ->call('save')
        ->assertHasErrors(['available_rooms' => 'lte']);
});

it('passes validation when available_rooms is equal to total_rooms', function () {
    Livewire::test(CreateKost::class)
        ->set('name', 'Kost Test')
        ->set('gender_type', 'campur')
        ->set('description', 'Deskripsi kost yang cukup panjang minimal sepuluh')
        ->set('district', 'Andir')
        ->set('address', 'Jl. Test No. 1')
        ->set('price_monthly', 500000)
        ->set('latitude', -6.918)
        ->set('longitude', 107.584)
        ->set('total_rooms', 10)
        ->set('available_rooms', 10)
        ->call('save')
        ->assertHasNoErrors(['available_rooms']);
});

it('passes validation when available_rooms is less than total_rooms', function () {
    Livewire::test(CreateKost::class)
        ->set('name', 'Kost Test')
        ->set('gender_type', 'campur')
        ->set('description', 'Deskripsi kost yang cukup panjang minimal sepuluh')
        ->set('district', 'Andir')
        ->set('address', 'Jl. Test No. 1')
        ->set('price_monthly', 500000)
        ->set('latitude', -6.918)
        ->set('longitude', 107.584)
        ->set('total_rooms', 10)
        ->set('available_rooms', 5)
        ->call('save')
        ->assertHasNoErrors(['available_rooms']);
});

it('fails validation when an extra period is selected but its price is empty', function () {
    Livewire::test(CreateKost::class)
        ->set('name', 'Kost Test')
        ->set('gender_type', 'campur')
        ->set('description', 'Deskripsi kost yang cukup panjang minimal sepuluh')
        ->set('district', 'Andir')
        ->set('address', 'Jl. Test No. 1')
        ->set('price_monthly', 500000)
        ->set('latitude', -6.918)
        ->set('longitude', 107.584)
        ->set('total_rooms', 10)
        ->set('available_rooms', 5)
        ->set('extraPeriods', ['six_monthly'])
        ->set('extraPeriodPrices.six_monthly', '')
        ->call('save')
        ->assertHasErrors(['extraPeriodPrices.six_monthly']);
});

it('fails validation when an extra period price is below the minimum', function () {
    Livewire::test(CreateKost::class)
        ->set('name', 'Kost Test')
        ->set('gender_type', 'campur')
        ->set('description', 'Deskripsi kost yang cukup panjang minimal sepuluh')
        ->set('district', 'Andir')
        ->set('address', 'Jl. Test No. 1')
        ->set('price_monthly', 500000)
        ->set('latitude', -6.918)
        ->set('longitude', 107.584)
        ->set('total_rooms', 10)
        ->set('available_rooms', 5)
        ->set('extraPeriods', ['six_monthly'])
        ->set('extraPeriodPrices.six_monthly', 5000)
        ->call('save')
        ->assertHasErrors(['extraPeriodPrices.six_monthly']);
});

it('passes validation and stores prices when extra periods have valid prices', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('test.jpg');

    Livewire::test(CreateKost::class)
        ->set('name', 'Kost Test')
        ->set('gender_type', 'campur')
        ->set('description', 'Deskripsi kost yang cukup panjang minimal sepuluh')
        ->set('district', 'Andir')
        ->set('address', 'Jl. Test No. 1')
        ->set('price_monthly', 500000)
        ->set('latitude', -6.918)
        ->set('longitude', 107.584)
        ->set('total_rooms', 10)
        ->set('available_rooms', 5)
        ->set('photos', [$image, $image, $image, $image])
        ->set('extraPeriods', ['six_monthly', 'yearly'])
        ->set('extraPeriodPrices.six_monthly', 2500000)
        ->set('extraPeriodPrices.yearly', 4800000)
        ->call('save')
        ->assertHasNoErrors(['extraPeriodPrices.six_monthly', 'extraPeriodPrices.yearly'])
        ->assertRedirect(route('dashboard'));

    $kost = Kost::where('name', 'Kost Test')->first();
    expect($kost)->not->toBeNull();

    $prices = $kost->prices()->pluck('price', 'period')->map(fn ($p) => (string) $p)->all();
    expect($prices)->toMatchArray([
        'six_monthly' => '2500000.00',
        'yearly' => '4800000.00',
    ]);
});

it('allows monthly as an extra period when the primary period is not monthly', function () {
    Livewire::test(CreateKost::class)
        ->set('name', 'Kost Test')
        ->set('gender_type', 'campur')
        ->set('description', 'Deskripsi kost yang cukup panjang minimal sepuluh')
        ->set('district', 'Andir')
        ->set('address', 'Jl. Test No. 1')
        ->set('price_monthly', 12000000)
        ->set('rent_period', 'yearly')
        ->set('latitude', -6.918)
        ->set('longitude', 107.584)
        ->set('total_rooms', 10)
        ->set('available_rooms', 5)
        ->set('extraPeriods', ['monthly'])
        ->set('extraPeriodPrices.monthly', 1000000)
        ->call('save')
        ->assertHasNoErrors(['rent_period', 'extraPeriods', 'extraPeriodPrices.monthly']);
});

it('addLandmarks appends sanitized items and syncs the string', function () {
    Livewire::test(CreateKost::class)
        ->set('landmarkList', [])
        ->call('addLandmarks', ['  Alfamidi  ', '120m Masjid Al-Ihsan', ''])
        ->assertSet('landmarkList', ['Alfamidi', '120m Masjid Al-Ihsan'])
        ->assertSet('nearby_landmarks', 'Alfamidi, 120m Masjid Al-Ihsan')
        ->assertDispatched('landmarks-added', added: 2);
});

it('addLandmarks skips duplicates case-insensitively', function () {
    Livewire::test(CreateKost::class)
        ->set('landmarkList', ['Alfamidi'])
        ->call('addLandmarks', ['alfamidi', 'ATM BCA'])
        ->assertSet('landmarkList', ['Alfamidi', 'ATM BCA'])
        ->assertDispatched('landmarks-added', added: 1);
});

it('addLandmarks dispatches zero count when nothing new is added', function () {
    Livewire::test(CreateKost::class)
        ->set('landmarkList', ['Alfamidi'])
        ->call('addLandmarks', ['Alfamidi'])
        ->assertDispatched('landmarks-added', added: 0);
});

it('addLandmarks caps the total landmark list at 12 items', function () {
    $existing = array_map(fn ($i) => 'Landmark '.$i, range(1, 10));
    Livewire::test(CreateKost::class)
        ->set('landmarkList', $existing)
        ->call('addLandmarks', ['Landmark A', 'Landmark B', 'Landmark C', 'Landmark D', 'Landmark E'])
        ->assertSet('landmarkList', array_merge($existing, ['Landmark A', 'Landmark B']))
        ->assertDispatched('landmarks-added', added: 2);
});

it('rejects an invalid rent period', function () {
    Livewire::test(CreateKost::class)
        ->set('name', 'Kost Test')
        ->set('gender_type', 'campur')
        ->set('description', 'Deskripsi kost yang cukup panjang minimal sepuluh')
        ->set('district', 'Andir')
        ->set('address', 'Jl. Test No. 1')
        ->set('price_monthly', 500000)
        ->set('rent_period', 'bulanan')
        ->set('latitude', -6.918)
        ->set('longitude', 107.584)
        ->set('total_rooms', 10)
        ->set('available_rooms', 5)
        ->call('save')
        ->assertHasErrors(['rent_period']);
});

it('removes the selected primary period from the extra periods', function () {
    Livewire::test(CreateKost::class)
        ->set('extraPeriods', ['yearly', 'six_monthly'])
        ->set('rent_period', 'yearly')
        ->assertSet('extraPeriods', ['six_monthly'])
        ->assertSet('extraPeriodPrices.yearly', '');
});

it('persists a yearly primary period and its price', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('test.jpg');

    Livewire::test(CreateKost::class)
        ->set('name', 'Kost Tahunan')
        ->set('gender_type', 'campur')
        ->set('description', 'Deskripsi kost yang cukup panjang minimal sepuluh')
        ->set('district', 'Andir')
        ->set('address', 'Jl. Test No. 2')
        ->set('price_monthly', 12000000)
        ->set('rent_period', 'yearly')
        ->set('latitude', -6.918)
        ->set('longitude', 107.584)
        ->set('total_rooms', 10)
        ->set('available_rooms', 5)
        ->set('photos', [$image, $image, $image, $image])
        ->call('save')
        ->assertHasNoErrors(['rent_period', 'price_monthly'])
        ->assertRedirect(route('dashboard'));

    $kost = Kost::where('name', 'Kost Tahunan')->first();
    expect($kost)->not->toBeNull();
    expect($kost->rent_period)->toBe('yearly');
    expect((float) $kost->price_monthly)->toBe(12000000.0);
});
