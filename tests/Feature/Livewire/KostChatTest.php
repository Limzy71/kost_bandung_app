<?php

use App\Livewire\Dashboard\OwnerChat;
use App\Livewire\Dashboard\SeekerChat;
use App\Models\Kost;
use App\Models\KostConversation;
use App\Models\KostMessage;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;

function kostChatTestKost(User $owner): Kost
{
    $name = 'Kost Chat '.Str::random(6);

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
        'status' => 'published',
        'total_rooms' => 5,
        'available_rooms' => 2,
    ]);
}

function kostChatTestConversation(User $seeker, Kost $kost, string $message = 'Apakah masih ada kamar?'): KostConversation
{
    $conversation = KostConversation::create([
        'kost_id' => $kost->id,
        'seeker_id' => $seeker->id,
        'status' => KostConversation::STATUS_OPEN,
    ]);

    KostMessage::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $seeker->id,
        'body' => $message,
    ]);

    return $conversation;
}

it('requires authentication to view the seeker chats page', function () {
    $this->get('/dashboard/user/chats')->assertRedirect(route('login'));
});

it('shows only the current user conversations on the seeker page', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $otherSeeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);

    $myConversation = kostChatTestConversation($seeker, $kost, 'Pesan milik saya.');
    kostChatTestConversation($otherSeeker, $kost, 'Pesan milik user lain.');

    $this->actingAs($seeker)
        ->get('/dashboard/user/chats')
        ->assertOk()
        ->assertSee($kost->name)
        ->assertSee('Pesan milik saya.')
        ->assertDontSee('Pesan milik user lain.');
});

it('shows the owner reply on the seeker chat page', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);

    $conversation = kostChatTestConversation($seeker, $kost);
    KostMessage::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $owner->id,
        'body' => 'Masih ada kamar kosong.',
    ]);

    $this->actingAs($seeker)
        ->get(route('user.chats', ['conversation' => $conversation->id]))
        ->assertOk()
        ->assertSee('Masih ada kamar kosong.');
});

it('marks incoming messages as read when the owner opens the conversation', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);
    $conversation = kostChatTestConversation($seeker, $kost);

    expect(KostMessage::where('read_at', null)->count())->toBe(1);

    Livewire::actingAs($owner)
        ->test(OwnerChat::class)
        ->call('openConversation', $conversation->id)
        ->assertSet('selectedConversationId', $conversation->id);

    expect(KostMessage::where('read_at', null)->count())->toBe(0);
});

it('lets the owner send a message to a conversation', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);
    $conversation = kostChatTestConversation($seeker, $kost);

    Livewire::actingAs($owner)
        ->test(OwnerChat::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('newBody', 'Masih ada kamar kosong.')
        ->call('sendMessage')
        ->assertHasNoErrors();

    expect(KostMessage::count())->toBe(2)
        ->and(KostMessage::latest('id')->first()->body)->toBe('Masih ada kamar kosong.')
        ->and(KostMessage::latest('id')->first()->sender_id)->toBe($owner->id);
});

it('prevents an owner from accessing another owners conversation', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $otherOwner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);
    $conversation = kostChatTestConversation($seeker, $kost);

    Livewire::actingAs($otherOwner)
        ->test(OwnerChat::class)
        ->call('openConversation', $conversation->id)
        ->assertForbidden();
});

it('prevents an owner from sending to another owners conversation', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $otherOwner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);
    $conversation = kostChatTestConversation($seeker, $kost);

    Livewire::actingAs($otherOwner)
        ->test(OwnerChat::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('newBody', 'Coba balas.')
        ->call('sendMessage')
        ->assertForbidden();
});

it('prevents a seeker from accessing another seekers conversation', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $otherSeeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);
    $conversation = kostChatTestConversation($seeker, $kost);

    Livewire::actingAs($otherSeeker)
        ->test(SeekerChat::class)
        ->call('openConversation', $conversation->id)
        ->assertForbidden();
});

it('lets the seeker send a follow-up message', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);
    $conversation = kostChatTestConversation($seeker, $kost);

    Livewire::actingAs($seeker)
        ->test(SeekerChat::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('newBody', 'Berapa harga per bulannya?')
        ->call('sendMessage')
        ->assertHasNoErrors();

    expect(KostMessage::count())->toBe(2)
        ->and(KostMessage::latest('id')->first()->sender_id)->toBe($seeker->id);
});

it('rejects an empty message', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);
    $conversation = kostChatTestConversation($seeker, $kost);

    Livewire::actingAs($owner)
        ->test(OwnerChat::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('newBody', '')
        ->call('sendMessage')
        ->assertHasErrors(['newBody' => 'required']);

    expect(KostMessage::count())->toBe(1);
});

it('hides the conversation for the owner after archiving and shows it again when the seeker replies', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);
    $conversation = kostChatTestConversation($seeker, $kost);

    Livewire::actingAs($owner)
        ->test(OwnerChat::class)
        ->call('toggleArchive', $conversation->id)
        ->assertHasNoErrors();

    expect($conversation->fresh()->status)->toBe(KostConversation::STATUS_ARCHIVED_BY_OWNER);

    Livewire::actingAs($owner)
        ->test(OwnerChat::class)
        ->assertDontSee($kost->name);

    KostMessage::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $seeker->id,
        'body' => 'Masih ada?',
    ]);
    $conversation->fresh()->forceFill(['status' => KostConversation::STATUS_OPEN])->save();

    Livewire::actingAs($owner)
        ->test(OwnerChat::class)
        ->assertSee($kost->name);
});

it('hides the conversation for the seeker after archiving', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);
    $conversation = kostChatTestConversation($seeker, $kost);

    Livewire::actingAs($seeker)
        ->test(SeekerChat::class)
        ->call('toggleArchive', $conversation->id)
        ->assertHasNoErrors();

    expect($conversation->fresh()->status)->toBe(KostConversation::STATUS_ARCHIVED_BY_SEEKER);

    Livewire::actingAs($seeker)
        ->test(SeekerChat::class)
        ->assertDontSee($kost->name);
});

it('reopens a conversation with a new message when the sender archives it', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);
    $conversation = kostChatTestConversation($seeker, $kost);

    Livewire::actingAs($seeker)
        ->test(SeekerChat::class)
        ->call('toggleArchive', $conversation->id);

    Livewire::actingAs($seeker)
        ->test(SeekerChat::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('newBody', 'Pesan baru membuka kembali.')
        ->call('sendMessage')
        ->assertHasNoErrors();

    expect($conversation->fresh()->status)->toBe(KostConversation::STATUS_OPEN);
});

it('marks the owners reply as read when the seeker opens the chat page', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = kostChatTestKost($owner);
    $conversation = kostChatTestConversation($seeker, $kost);
    $ownerReply = KostMessage::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $owner->id,
        'body' => 'Masih tersedia.',
    ]);

    $this->actingAs($seeker)
        ->get(route('user.chats', ['conversation' => $conversation->id]))
        ->assertOk();

    expect($ownerReply->fresh()->read_at)->not->toBeNull()
        ->and(KostMessage::where('conversation_id', $conversation->id)
            ->whereNull('read_at')
            ->where('sender_id', $seeker->id)
            ->count())->toBe(1);
});

it('renders the seeker chat livewire component for a pencari kost', function () {
    $seeker = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($seeker)
        ->test(SeekerChat::class)
        ->assertViewHas('conversations');
});

it('renders the owner chat livewire component for a pemilik kost', function () {
    $owner = User::factory()->create(['role' => 'owner']);

    Livewire::actingAs($owner)
        ->test(OwnerChat::class)
        ->assertViewHas('conversations');
});
