<?php

use App\Livewire\Dashboard\EditKost;
use App\Models\Facility;
use App\Models\Kost;
use App\Models\KostImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Livewire\Livewire;

function makeOwnerKost(User $user, int $imageCount = 4): Kost
{
    $kost = Kost::create([
        'user_id' => $user->id,
        'name' => 'Kost Owner ' . $user->id,
        'slug' => 'kost-owner-' . $user->id . '-' . Str::random(6),
        'description' => 'Deskripsi kost yang cukup panjang minimal sepuluh',
        'gender_type' => 'campur',
        'price_monthly' => 1000000,
        'rent_period' => 'monthly',
        'address' => 'Jl. Test No. 1',
        'district' => 'Andir',
        'latitude' => -6.918,
        'longitude' => 107.584,
        'status' => 'published',
        'total_rooms' => 10,
        'available_rooms' => 5,
    ]);

    for ($i = 0; $i < $imageCount; $i++) {
        KostImage::create([
            'kost_id' => $kost->id,
            'image_path' => 'kosts/test-' . $kost->id . '-' . $i . '.jpg',
            'is_primary' => $i === 0,
        ]);
    }

    return $kost;
}

it('aborts with 403 when a non-owner tries to edit a kost', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $kost = makeOwnerKost($owner);

    $this->actingAs($intruder);

    Livewire::test(EditKost::class, ['kost' => $kost])
        ->assertForbidden();
});

it('fails when total photos are below the 4 minimum', function () {
    $user = User::factory()->create();
    $kost = makeOwnerKost($user, 3);

    $this->actingAs($user);

    Livewire::test(EditKost::class, ['kost' => $kost])
        ->call('save')
        ->assertHasErrors(['photos']);
});

it('fails when total photos exceed the 10 maximum', function () {
    $user = User::factory()->create();
    $kost = makeOwnerKost($user, 4);

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('new.jpg');

    Livewire::test(EditKost::class, ['kost' => $kost])
        ->set('photos', array_fill(0, 7, $image))
        ->call('save')
        ->assertHasErrors(['photos']);
});

it('passes validation when total photos are exactly 10', function () {
    $user = User::factory()->create();
    $kost = makeOwnerKost($user, 4);

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('new.jpg');

    Livewire::test(EditKost::class, ['kost' => $kost])
        ->set('photos', array_fill(0, 6, $image))
        ->call('save')
        ->assertHasNoErrors(['photos']);
});

it('removes an orphaned pending facility on save and allows re-adding its name', function () {
    $user = User::factory()->create();
    $kost = makeOwnerKost($user, 4);

    $facility = Facility::create([
        'name' => 'Kolam Renang Unik',
        'type' => 'building',
        'status' => 'pending',
        'user_id' => $user->id,
    ]);
    $kost->facilities()->attach($facility->id);

    $this->actingAs($user);

    Livewire::test(EditKost::class, ['kost' => $kost])
        ->call('removeCustomFacility', 0)
        ->call('save')
        ->assertHasNoErrors();

    expect(Facility::find($facility->id))->toBeNull();

    Livewire::test(EditKost::class, ['kost' => $kost])
        ->set('newBuildingFacility', 'Kolam Renang Unik')
        ->call('addFacility', 'building')
        ->assertHasNoErrors(['newBuildingFacility']);
});

it('keeps a pending facility that is still selected on save', function () {
    $user = User::factory()->create();
    $kost = makeOwnerKost($user, 4);

    $facility = Facility::create([
        'name' => 'Parkiran Luas',
        'type' => 'building',
        'status' => 'pending',
        'user_id' => $user->id,
    ]);
    $kost->facilities()->attach($facility->id);

    $this->actingAs($user);

    Livewire::test(EditKost::class, ['kost' => $kost])
        ->call('save')
        ->assertHasNoErrors();

    expect(Facility::find($facility->id))->not->toBeNull();
});
