<?php

use App\Livewire\KostDetail;
use App\Models\Kost;
use App\Models\KostConversation;
use App\Models\KostMessage;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;

function kostDetailTestKost(User $owner, string $status = 'published', string $name = '', bool $isAvailable = true): Kost
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
        'is_available' => $isAvailable,
        'status' => $status,
        'total_rooms' => 5,
        'available_rooms' => 2,
    ]);
}

it('redirects guests to login when they try to start a chat', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostDetailTestKost($owner);

    Livewire::test(KostDetail::class, ['kost' => $kost])
        ->set('message_name', 'Budi')
        ->set('message_phone', '081234567890')
        ->set('message_body', 'Apakah kamar masih tersedia?')
        ->call('startChat')
        ->assertRedirect(route('login'));

    expect(KostConversation::count())->toBe(0)
        ->and(KostMessage::count())->toBe(0);
});

it('creates a conversation and message when an authenticated user sends a chat', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $seeker = User::factory()->create(['role' => 'user']);
    $kost = kostDetailTestKost($owner);

    Livewire::actingAs($seeker)
        ->test(KostDetail::class, ['kost' => $kost])
        ->set('message_name', 'Budi')
        ->set('message_phone', '081234567890')
        ->set('message_body', 'Apakah kamar masih tersedia?')
        ->call('startChat')
        ->assertHasNoErrors()
        ->assertRedirect();

    $conversation = KostConversation::first();

    expect(KostConversation::count())->toBe(1)
        ->and($conversation->kost_id)->toBe($kost->id)
        ->and($conversation->seeker_id)->toBe($seeker->id)
        ->and(KostMessage::count())->toBe(1)
        ->and(KostMessage::first()->body)->toBe('Apakah kamar masih tersedia?')
        ->and(KostMessage::first()->sender_id)->toBe($seeker->id);
});

it('redirects the seeker to their chat thread after starting a chat', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $seeker = User::factory()->create(['role' => 'user']);
    $kost = kostDetailTestKost($owner);

    Livewire::actingAs($seeker)
        ->test(KostDetail::class, ['kost' => $kost])
        ->set('message_name', 'Budi')
        ->set('message_phone', '081234567890')
        ->set('message_body', 'Apakah kamar masih tersedia?')
        ->call('startChat')
        ->assertRedirect(route('user.chats', ['conversation' => KostConversation::first()->id]));
});

it('reuses an existing conversation when the seeker sends again', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $seeker = User::factory()->create(['role' => 'user']);
    $kost = kostDetailTestKost($owner);

    KostConversation::create([
        'kost_id' => $kost->id,
        'seeker_id' => $seeker->id,
        'status' => KostConversation::STATUS_OPEN,
    ]);

    Livewire::actingAs($seeker)
        ->test(KostDetail::class, ['kost' => $kost])
        ->set('message_name', 'Budi')
        ->set('message_phone', '081234567890')
        ->set('message_body', 'Pesan kedua.')
        ->call('startChat')
        ->assertHasNoErrors();

    expect(KostConversation::count())->toBe(1)
        ->and(KostMessage::count())->toBe(1)
        ->and(KostMessage::first()->body)->toBe('Pesan kedua.');
});

it('prefills the message name and phone from the logged in profile', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $seeker = User::factory()->create(['role' => 'user', 'phone_number' => '081234567890']);
    $kost = kostDetailTestKost($owner);

    Livewire::actingAs($seeker)
        ->test(KostDetail::class, ['kost' => $kost])
        ->assertSet('message_name', $seeker->name)
        ->assertSet('message_phone', '081234567890');
});

it('leaves the message phone empty when the profile has no phone number', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $seeker = User::factory()->create(['role' => 'user', 'phone_number' => null]);
    $kost = kostDetailTestKost($owner);

    Livewire::actingAs($seeker)
        ->test(KostDetail::class, ['kost' => $kost])
        ->assertSet('message_name', $seeker->name)
        ->assertSet('message_phone', '');
});

it('blocks the chat when the kost is full', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $seeker = User::factory()->create(['role' => 'user']);
    $kost = kostDetailTestKost($owner, isAvailable: false);

    Livewire::actingAs($seeker)
        ->test(KostDetail::class, ['kost' => $kost])
        ->set('message_name', 'Budi')
        ->set('message_phone', '081234567890')
        ->set('message_body', 'Apakah kamar masih tersedia?')
        ->call('startChat')
        ->assertHasErrors('message_body');

    expect(KostConversation::count())->toBe(0)
        ->and(KostMessage::count())->toBe(0);
});

it('blocks the chat when the kost becomes unpublished after the page loaded', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $seeker = User::factory()->create(['role' => 'user']);
    $kost = kostDetailTestKost($owner);

    $component = Livewire::actingAs($seeker)
        ->test(KostDetail::class, ['kost' => $kost])
        ->set('message_name', 'Budi')
        ->set('message_phone', '081234567890')
        ->set('message_body', 'Apakah kamar masih tersedia?');

    $kost->update(['status' => 'rejected']);

    $component->call('startChat')
        ->assertHasErrors('message_body');

    expect(KostConversation::count())->toBe(0);
});

it('blocks the owner from starting a chat with their own kost', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostDetailTestKost($owner);

    Livewire::actingAs($owner)
        ->test(KostDetail::class, ['kost' => $kost])
        ->set('message_name', $owner->name)
        ->set('message_phone', '081234567890')
        ->set('message_body', 'Apakah kamar masih tersedia?')
        ->call('startChat')
        ->assertHasErrors('message_body');

    expect(KostConversation::count())->toBe(0)
        ->and(KostMessage::count())->toBe(0);
});

it('shows a notice when the kost was deleted after the page loaded', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $seeker = User::factory()->create(['role' => 'user']);
    $kost = kostDetailTestKost($owner);

    $component = Livewire::actingAs($seeker)
        ->test(KostDetail::class, ['kost' => $kost])
        ->set('message_name', 'Budi')
        ->set('message_phone', '081234567890')
        ->set('message_body', 'Apakah kamar masih tersedia?');

    $kost->forceDelete();

    $component->call('startChat')
        ->assertSet('kostUnavailable', true)
        ->assertDispatched('kost-unavailable')
        ->assertSee('Kost Tidak Tersedia')
        ->assertDontSee('Kirim Pesan ke Pemilik');

    expect(KostConversation::count())->toBe(0);
});
