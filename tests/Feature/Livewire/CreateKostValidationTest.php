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
