<?php

use App\Livewire\Contact\AdminChat;
use App\Models\AdminConversation;
use App\Models\AdminMessage;
use App\Models\User;
use Livewire\Livewire;

function adminChatUser(): User
{
    return User::factory()->create(['role' => 'user']);
}

function adminChatConversation(User $user, string $category = 'pertanyaan'): AdminConversation
{
    return AdminConversation::create([
        'user_id' => $user->id,
        'sender_role' => $user->role,
        'category' => $category,
        'status' => 'open',
        'awaiting_reply_at' => now(),
    ]);
}

function adminChatMessage(AdminConversation $conversation, string $body = 'Apakah masih ada kamar?'): AdminMessage
{
    return AdminMessage::create([
        'conversation_id' => $conversation->id,
        'sender_type' => 'user',
        'sender_id' => $conversation->user_id,
        'body' => $body,
    ]);
}

it('requires authentication to view the hubungi admin page', function () {
    $this->get('/hubungi-admin')->assertRedirect(route('login'));
});

it('forbids an admin from using the chat as a sender', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get('/hubungi-admin')->assertForbidden();
});

it('lets a pencari kost open a new conversation', function () {
    $user = adminChatUser();

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->set('category', 'komplain')
        ->set('newBody', 'Kamar saya bocor.')
        ->call('sendNewConversation')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('admin_conversations', [
        'user_id' => $user->id,
        'sender_role' => 'user',
        'category' => 'komplain',
        'status' => 'open',
    ]);

    $this->assertDatabaseHas('admin_messages', [
        'sender_type' => 'user',
        'sender_id' => $user->id,
        'body' => 'Kamar saya bocor.',
    ]);
});

it('lets an owner open a new conversation', function () {
    $owner = User::factory()->create(['role' => 'owner']);

    Livewire::actingAs($owner)
        ->test(AdminChat::class)
        ->set('category', 'pertanyaan')
        ->set('newBody', 'Bagaimana cara verifikasi iklan?')
        ->call('sendNewConversation')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('admin_conversations', [
        'user_id' => $owner->id,
        'sender_role' => 'owner',
        'category' => 'pertanyaan',
        'status' => 'open',
    ]);
});

it('rejects a new conversation without a category', function () {
    $user = adminChatUser();

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->set('category', '')
        ->set('newBody', 'Pesan tanpa kategori.')
        ->call('sendNewConversation')
        ->assertHasErrors(['category' => 'required']);
});

it('rejects an empty message body', function () {
    $user = adminChatUser();

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->set('category', 'masukan')
        ->set('newBody', '')
        ->call('sendNewConversation')
        ->assertHasErrors(['newBody' => 'required']);
});

it('resets the awaiting reply deadline when the user sends a follow up', function () {
    $user = adminChatUser();
    $conversation = adminChatConversation($user);

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('followUpBody', 'Update: sudah dicek.')
        ->call('sendFollowUp')
        ->assertHasNoErrors();

    $fresh = $conversation->fresh();

    expect($fresh->awaiting_reply_at)->not->toBeNull();
    expect($fresh->isOpen())->toBeTrue();

    $this->assertDatabaseHas('admin_messages', [
        'conversation_id' => $conversation->id,
        'sender_type' => 'user',
        'body' => 'Update: sudah dicek.',
    ]);
});

it('blocks a follow up when the conversation is closed', function () {
    $user = adminChatUser();
    $conversation = adminChatConversation($user);
    $conversation->update([
        'status' => 'closed',
        'closed_reason' => 'admin',
        'closed_at' => now(),
        'awaiting_reply_at' => null,
    ]);

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('followUpBody', 'Coba balas lagi.')
        ->call('sendFollowUp')
        ->assertHasErrors(['followUpBody' => 'Percakapan ini sudah ditutup. Silakan buka percakapan baru untuk menghubungi Admin.']);

    expect(AdminMessage::where('conversation_id', $conversation->id)->count())->toBe(0);
});

it('forbids a user from following up on another users conversation', function () {
    $user = adminChatUser();
    $otherUser = adminChatUser();
    $conversation = adminChatConversation($otherUser);

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('followUpBody', 'Halo?')
        ->call('sendFollowUp')
        ->assertForbidden();
});

it('forbids a user from opening another users conversation', function () {
    $user = adminChatUser();
    $otherUser = adminChatUser();
    $conversation = adminChatConversation($otherUser);

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->call('openConversation', $conversation->id)
        ->assertForbidden();
});

it('shows only the current users conversations', function () {
    $user = adminChatUser();
    $otherUser = adminChatUser();
    $mine = adminChatConversation($user, 'komplain');
    adminChatMessage($mine, 'Pesan milik saya.');
    $theirs = adminChatConversation($otherUser, 'masukan');
    adminChatMessage($theirs, 'Pesan milik user lain.');

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->assertSee('Pesan milik saya.')
        ->assertDontSee('Pesan milik user lain.');
});

it('automatically expires conversations unanswered for more than 24 hours', function () {
    $user = adminChatUser();
    $conversation = adminChatConversation($user);
    $conversation->update(['awaiting_reply_at' => now()->subHours(25)]);

    $expired = AdminConversation::expireStale();

    expect($expired)->toBe(1);

    $fresh = $conversation->fresh();

    expect($fresh->status)->toBe('closed');
    expect($fresh->closed_reason)->toBe('expired');
    expect($fresh->awaiting_reply_at)->toBeNull();
    expect($fresh->closed_at)->not->toBeNull();
});

it('does not expire conversations still within the 24 hour window', function () {
    $user = adminChatUser();
    $conversation = adminChatConversation($user);
    $conversation->update(['awaiting_reply_at' => now()->subHours(23)]);

    AdminConversation::expireStale();

    expect($conversation->fresh()->isOpen())->toBeTrue();
});

it('blocks a follow up on an automatically expired conversation', function () {
    $user = adminChatUser();
    $conversation = adminChatConversation($user);
    $conversation->update(['awaiting_reply_at' => now()->subHours(25)]);

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('followUpBody', 'Masih ada?')
        ->call('sendFollowUp')
        ->assertHasErrors(['followUpBody' => 'Percakapan ini sudah ditutup. Silakan buka percakapan baru untuk menghubungi Admin.']);
});

it('marks admin replies as read when the user opens the page', function () {
    $user = adminChatUser();
    $conversation = adminChatConversation($user);
    AdminMessage::create([
        'conversation_id' => $conversation->id,
        'sender_type' => 'admin',
        'sender_id' => User::factory()->create(['role' => 'admin'])->id,
        'body' => 'Balasan admin.',
    ]);

    Livewire::actingAs($user)->test(AdminChat::class);

    $this->assertDatabaseHas('admin_messages', [
        'conversation_id' => $conversation->id,
        'sender_type' => 'admin',
        'read_at' => now(),
    ]);
});

it('paginates the conversation list', function () {
    $user = adminChatUser();

    foreach (range(1, 16) as $i) {
        $conversation = adminChatConversation($user, 'lainnya');
        adminChatMessage($conversation, 'Pesan ke-'.$i);
    }

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->assertViewHas('conversations', function ($paginator) {
            return $paginator->count() === 15;
        });
});

it('shows the second page of conversations', function () {
    $user = adminChatUser();

    foreach (range(1, 16) as $i) {
        $conversation = adminChatConversation($user, 'lainnya');
        adminChatMessage($conversation, 'Pesan ke-'.$i);
    }

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->call('setPage', 2)
        ->assertViewHas('conversations', function ($paginator) {
            return $paginator->count() === 1;
        });
});

it('purges soft deleted conversations after 30 days including messages', function () {
    $user = adminChatUser();
    $conversation = adminChatConversation($user);
    adminChatMessage($conversation, 'Pesan yang akan hilang.');
    $conversation->delete();
    $conversation->forceFill(['deleted_at' => now()->subDays(31)])->save();

    $purged = AdminConversation::pruneSoftDeleted();

    expect($purged)->toBe(1);
    expect(AdminConversation::withTrashed()->find($conversation->id))->toBeNull();
    expect(AdminMessage::where('conversation_id', $conversation->id)->count())->toBe(0);
});
