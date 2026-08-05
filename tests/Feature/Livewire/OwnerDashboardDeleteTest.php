<?php

use App\Livewire\Dashboard\OwnerDashboard;
use App\Models\Inquiry;
use App\Models\Kost;
use App\Models\KostImage;
use App\Models\KostPrice;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

function ownerDashboardTestKost(User $owner, string $status = 'published'): Kost
{
    $name = 'Kost Hapus '.Str::random(6);

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

it('permanently deletes an owned kost along with its related data', function () {
    Storage::fake('local');

    $owner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);
    $kost = ownerDashboardTestKost($owner);

    KostImage::create([
        'kost_id' => $kost->id,
        'image_path' => 'kosts/photo.jpg',
        'is_primary' => true,
    ]);
    Storage::put('kosts/photo.jpg', 'fake-content');

    KostPrice::create(['kost_id' => $kost->id, 'period' => 'weekly', 'price' => 500000]);

    Inquiry::create([
        'kost_id' => $kost->id,
        'name' => 'Budi',
        'phone_number' => '081234567890',
        'message' => 'Masih ada kamar?',
    ]);

    Livewire::actingAs($owner)
        ->test(OwnerDashboard::class)
        ->set('deleteTargetId', $kost->id)
        ->set('deleteTargetName', $kost->name)
        ->set('deleteConfirmText', 'HAPUS')
        ->call('deleteKost')
        ->assertHasNoErrors()
        ->assertSet('deleteTargetId', null)
        ->assertSet('deleteConfirmText', '')
        ->assertDispatched('show-toast');

    expect(Kost::withTrashed()->count())->toBe(0)
        ->and(Kost::count())->toBe(0)
        ->and(KostImage::count())->toBe(0)
        ->and(KostPrice::count())->toBe(0)
        ->and(Inquiry::count())->toBe(0);

    Storage::assertMissing('kosts/photo.jpg');
});

it('makes a deleted kost return 404 on the public detail page', function () {
    $owner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);
    $kost = ownerDashboardTestKost($owner);

    Livewire::actingAs($owner)
        ->test(OwnerDashboard::class)
        ->set('deleteTargetId', $kost->id)
        ->set('deleteTargetName', $kost->name)
        ->set('deleteConfirmText', 'HAPUS')
        ->call('deleteKost');

    $this->get(route('kost.show', $kost->slug))->assertNotFound();
});

it('does not delete a kost owned by another user', function () {
    $owner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);
    $otherOwner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);
    $kost = ownerDashboardTestKost($owner);

    Livewire::actingAs($otherOwner)
        ->test(OwnerDashboard::class)
        ->set('deleteTargetId', $kost->id)
        ->set('deleteTargetName', $kost->name)
        ->set('deleteConfirmText', 'HAPUS')
        ->call('deleteKost');

    expect(Kost::count())->toBe(1)
        ->and(Kost::where('id', $kost->id)->exists())->toBeTrue();
});

it('requires the HAPUS confirmation text before deleting', function () {
    $owner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);
    $kost = ownerDashboardTestKost($owner);

    Livewire::actingAs($owner)
        ->test(OwnerDashboard::class)
        ->set('deleteTargetId', $kost->id)
        ->set('deleteTargetName', $kost->name)
        ->set('deleteConfirmText', 'HAPUSX')
        ->call('deleteKost')
        ->assertHasErrors('deleteConfirmText');

    expect(Kost::count())->toBe(1);
});

it('accepts lowercase confirmation text for deletion', function () {
    $owner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);
    $kost = ownerDashboardTestKost($owner);

    Livewire::actingAs($owner)
        ->test(OwnerDashboard::class)
        ->set('deleteTargetId', $kost->id)
        ->set('deleteTargetName', $kost->name)
        ->set('deleteConfirmText', 'hapus')
        ->call('deleteKost')
        ->assertHasNoErrors();

    expect(Kost::count())->toBe(0);
});
