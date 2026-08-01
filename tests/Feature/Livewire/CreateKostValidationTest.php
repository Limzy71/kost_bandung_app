<?php

use App\Livewire\Dashboard\CreateKost;
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
    $user = \App\Models\User::factory()->create();
    $this->actingAs($user);

    $image = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');

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

    $kost = \App\Models\Kost::where('name', 'Kost Test')->first();
    expect($kost)->not->toBeNull();

    $prices = $kost->prices()->pluck('price', 'period')->map(fn ($p) => (string) $p)->all();
    expect($prices)->toMatchArray([
        'six_monthly' => '2500000.00',
        'yearly' => '4800000.00',
    ]);
});
