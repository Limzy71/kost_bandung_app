<?php

use App\Livewire\KostDetail;
use App\Models\Inquiry;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;

function kostDetailTestKost(User $owner, string $status = 'published', string $name = ''): Kost
{
    $name = $name ?: 'Kost '.Str::random(6);

    return Kost::create([
        'user_id' => $owner->id,
        'name' => $name,
        'slug' => Str::slug($name),
        'description' => 'Deskripsi kost yang cukup panjang minimal sepuluh kata.',
        'gender_type' => 'campur',
        'price_monthly' => 1000000,
        'rent_period' => 'monthly',
        'address' => 'Jl. Test No. 1',
        'district' => 'Andir',
        'latitude' => -6.918,
        'longitude' => 107.584,
        'is_available' => true,
        'status' => $status,
        'total_rooms' => 5,
        'available_rooms' => 2,
    ]);
}

it('redirects guests to login when they try to send an inquiry', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostDetailTestKost($owner);

    Livewire::test(KostDetail::class, ['kost' => $kost])
        ->set('inquiry_name', 'Budi')
        ->set('inquiry_phone', '081234567890')
        ->set('inquiry_message', 'Apakah kamar masih tersedia?')
        ->call('sendInquiry')
        ->assertRedirect(route('login'));

    expect(Inquiry::count())->toBe(0);
});

it('stores an inquiry when an authenticated user sends one', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $seeker = User::factory()->create(['role' => 'user']);
    $kost = kostDetailTestKost($owner);

    Livewire::actingAs($seeker)
        ->test(KostDetail::class, ['kost' => $kost])
        ->set('inquiry_name', 'Budi')
        ->set('inquiry_phone', '081234567890')
        ->set('inquiry_message', 'Apakah kamar masih tersedia?')
        ->call('sendInquiry')
        ->assertHasNoErrors()
        ->assertDispatched('inquiry-sent');

    expect(Inquiry::count())->toBe(1)
        ->and(Inquiry::first()->kost_id)->toBe($kost->id)
        ->and(Inquiry::first()->user_id)->toBe($seeker->id);
});
