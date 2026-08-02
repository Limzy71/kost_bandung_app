<?php

use App\Livewire\Profile\Index;
use App\Models\Inquiry;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;

function profileTestKost(User $owner, string $status = 'published', string $name = ''): Kost
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

it('requires authentication to view the profile page', function () {
    $this->get('/profil')->assertRedirect(route('login'));
});

it('renders the profile page for a pencari kost', function () {
    $user = User::factory()->create(['role' => 'user', 'phone_number' => '081234567890']);

    $this->actingAs($user)
        ->get('/profil')
        ->assertOk()
        ->assertSee('Profil Saya')
        ->assertSee($user->name)
        ->assertSee('081234567890');
});

it('shows inquiry history and stats for a pencari kost', function () {
    $user = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = profileTestKost($owner);

    Inquiry::create([
        'kost_id' => $kost->id,
        'user_id' => $user->id,
        'name' => $user->name,
        'phone_number' => '081234567890',
        'message' => 'Apakah masih ada kamar?',
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->get('/profil')
        ->assertOk()
        ->assertSee($kost->name)
        ->assertSee('Riwayat Pertanyaan Saya');
});

it('renders owner specific sections on the profile page', function () {
    $owner = User::factory()->create(['role' => 'owner', 'business_name' => 'Kost Sejahtera']);
    profileTestKost($owner, 'published');

    $this->actingAs($owner)
        ->get('/profil')
        ->assertOk()
        ->assertSee('Kost Sejahtera')
        ->assertSee('Daftar Kost Saya')
        ->assertSee('Menunggu Moderasi');
});

it('renders admin specific sections on the profile page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/profil')
        ->assertOk()
        ->assertSee('Panel Moderasi')
        ->assertSee('Total Pencari Kost');
});

it('updates the profile name and email', function () {
    $user = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('name', 'Nama Baru')
        ->set('email', 'baru@example.com')
        ->call('updateProfile')
        ->assertHasNoErrors()
        ->assertDispatched('show-toast');

    expect($user->fresh()->name)->toBe('Nama Baru');
    expect($user->fresh()->email)->toBe('baru@example.com');
});

it('resets email verification when the email is changed', function () {
    $user = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('email', 'baru@example.com')
        ->call('updateProfile')
        ->assertHasNoErrors();

    expect($user->fresh()->email_verified_at)->toBeNull();
});

it('rejects a duplicate email', function () {
    $user = User::factory()->create(['role' => 'user']);
    User::factory()->create(['role' => 'user', 'email' => 'dipakai@example.com']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('email', 'dipakai@example.com')
        ->call('updateProfile')
        ->assertHasErrors(['email' => 'unique']);
});

it('lets an owner update phone number and business name', function () {
    $owner = User::factory()->create(['role' => 'owner']);

    Livewire::actingAs($owner)
        ->test(Index::class)
        ->set('phone_number', '081298765432')
        ->set('business_name', 'Kost Baru Sejahtera')
        ->call('updateProfile')
        ->assertHasNoErrors();

    expect($owner->fresh()->phone_number)->toBe('081298765432');
    expect($owner->fresh()->business_name)->toBe('Kost Baru Sejahtera');
});

it('requires a business name for owners', function () {
    $owner = User::factory()->create(['role' => 'owner']);

    Livewire::actingAs($owner)
        ->test(Index::class)
        ->set('business_name', '')
        ->call('updateProfile')
        ->assertHasErrors(['business_name' => 'required']);
});
