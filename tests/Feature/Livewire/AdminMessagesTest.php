<?php

use App\Livewire\Admin\AdminMessages;
use App\Models\AdminConversation;
use App\Models\AdminMessage;
use App\Models\User;
use Livewire\Livewire;

function adminMessagesUser(string $role = 'user'): User
{
    return User::factory()->create(['role' => $role]);
}

function adminMessagesConversation(User $user, string $category = 'pertanyaan'): AdminConversation
{
    return AdminConversation::create([
        'user_id' => $user->id,
        'sender_role' => $user->role,
        'category' => $category,
        'status' => 'open',
        'awaiting_reply_at' => now(),
    ]);
}

function adminMessagesUserMessage(AdminConversation $conversation, string $body = 'Mohon dibantu.'): AdminMessage
{
    return AdminMessage::create([
        'conversation_id' => $conversation->id,
        'sender_type' => 'user',
        'sender_id' => $conversation->user_id,
        'body' => $body,
    ]);
}

it('requires authentication to view the admin messages page', function () {
    $this->get('/admin/messages')->assertRedirect(route('login'));
});

it('forbids a pencari kost from accessing the admin messages page', function () {
    $user = adminMessagesUser();

    $this->actingAs($user)->get('/admin/messages')->assertForbidden();
});

it('forbids an owner from accessing the admin messages page', function () {
    $owner = adminMessagesUser('owner');

    $this->actingAs($owner)->get('/admin/messages')->assertForbidden();
});

it('lets an admin see unanswered conversations', function () {
    $admin = adminMessagesUser('admin');
    $user = adminMessagesUser();
    $conversation = adminMessagesConversation($user);
    adminMessagesUserMessage($conversation, 'Mohon dibantu dong.');

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->assertSee('Mohon dibantu dong.')
        ->assertSee($user->name);
});

it('marks user messages as read when the admin opens the conversation', function () {
    $admin = adminMessagesUser('admin');
    $user = adminMessagesUser();
    $conversation = adminMessagesConversation($user);
    adminMessagesUserMessage($conversation, 'Pesan belum dibaca.');

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->call('openConversation', $conversation->id);

    $this->assertDatabaseHas('admin_messages', [
        'conversation_id' => $conversation->id,
        'sender_type' => 'user',
        'read_at' => now(),
    ]);
});

it('lets the admin reply to an open conversation', function () {
    $admin = adminMessagesUser('admin');
    $user = adminMessagesUser();
    $conversation = adminMessagesConversation($user);
    adminMessagesUserMessage($conversation);

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('replyBody', 'Baik, akan kami cek.')
        ->call('replyConversation')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('admin_messages', [
        'conversation_id' => $conversation->id,
        'sender_type' => 'admin',
        'body' => 'Baik, akan kami cek.',
    ]);

    $fresh = $conversation->fresh();

    expect($fresh->isOpen())->toBeTrue();
    expect($fresh->awaiting_reply_at)->toBeNull();
});

it('rejects an empty admin reply', function () {
    $admin = adminMessagesUser('admin');
    $user = adminMessagesUser();
    $conversation = adminMessagesConversation($user);
    adminMessagesUserMessage($conversation);

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('replyBody', '')
        ->call('replyConversation')
        ->assertHasErrors(['replyBody' => 'required']);
});

it('blocks an admin reply on a closed conversation', function () {
    $admin = adminMessagesUser('admin');
    $user = adminMessagesUser();
    $conversation = adminMessagesConversation($user);
    adminMessagesUserMessage($conversation);
    $conversation->update([
        'status' => 'closed',
        'closed_reason' => 'admin',
        'closed_at' => now(),
        'awaiting_reply_at' => null,
    ]);

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->set('selectedConversationId', $conversation->id)
        ->set('replyBody', 'Coba balas.')
        ->call('replyConversation')
        ->assertHasErrors(['replyBody' => 'Percakapan sudah ditutup dan tidak dapat dibalas lagi.']);
});

it('lets the admin close an open conversation', function () {
    $admin = adminMessagesUser('admin');
    $user = adminMessagesUser();
    $conversation = adminMessagesConversation($user);
    adminMessagesUserMessage($conversation);

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->call('closeConversation', $conversation->id);

    $fresh = $conversation->fresh();

    expect($fresh->status)->toBe('closed');
    expect($fresh->closed_reason)->toBe('admin');
    expect($fresh->closed_at)->not->toBeNull();
    expect($fresh->awaiting_reply_at)->toBeNull();
});

it('soft deletes a closed conversation via the delete action', function () {
    $admin = adminMessagesUser('admin');
    $user = adminMessagesUser();
    $conversation = adminMessagesConversation($user);
    adminMessagesUserMessage($conversation);
    $conversation->update([
        'status' => 'closed',
        'closed_reason' => 'admin',
        'closed_at' => now(),
        'awaiting_reply_at' => null,
    ]);

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->call('deleteConversation', $conversation->id);

    $this->assertSoftDeleted('admin_conversations', ['id' => $conversation->id]);

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->set('filter', 'history')
        ->assertDontSee($user->name);
});

it('prevents deleting an open conversation', function () {
    $admin = adminMessagesUser('admin');
    $user = adminMessagesUser();
    $conversation = adminMessagesConversation($user);
    adminMessagesUserMessage($conversation);

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->call('deleteConversation', $conversation->id)
        ->assertHasErrors(['replyBody' => 'Percakapan masih aktif. Tutup terlebih dahulu sebelum menghapus.']);

    expect($conversation->fresh()->isOpen())->toBeTrue();
});

it('filters conversations by status', function () {
    $admin = adminMessagesUser('admin');
    $user = adminMessagesUser();

    $open = adminMessagesConversation($user);
    adminMessagesUserMessage($open, 'Masih menunggu.');

    $closed = adminMessagesConversation($user, 'masukan');
    adminMessagesUserMessage($closed, 'Pesan lama.');
    $closed->update([
        'status' => 'closed',
        'closed_reason' => 'admin',
        'closed_at' => now(),
        'awaiting_reply_at' => null,
    ]);

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->set('filter', 'unanswered')
        ->assertViewHas('counts', function ($counts) {
            return $counts['unanswered'] === 1;
        });

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->set('filter', 'history')
        ->assertViewHas('counts', function ($counts) {
            return $counts['history'] === 1;
        });
});

it('paginates the admin conversation list', function () {
    $admin = adminMessagesUser('admin');
    $user = adminMessagesUser();

    foreach (range(1, 16) as $i) {
        $conversation = adminMessagesConversation($user, 'lainnya');
        adminMessagesUserMessage($conversation, 'Pesan ke-'.$i);
    }

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->assertViewHas('conversations', function ($paginator) {
            return $paginator->count() === 15;
        })
        ->call('setPage', 2)
        ->assertViewHas('conversations', function ($paginator) {
            return $paginator->count() === 1;
        });
});

it('purges soft deleted conversations after 30 days from the admin side', function () {
    $admin = adminMessagesUser('admin');
    $user = adminMessagesUser();
    $conversation = adminMessagesConversation($user);
    adminMessagesUserMessage($conversation, 'Pesan lama.');
    $conversation->delete();
    $conversation->forceFill(['deleted_at' => now()->subDays(31)])->save();

    $purged = AdminConversation::pruneSoftDeleted();

    expect($purged)->toBe(1);
    expect(AdminConversation::withTrashed()->find($conversation->id))->toBeNull();
    expect(AdminMessage::where('conversation_id', $conversation->id)->count())->toBe(0);
});
