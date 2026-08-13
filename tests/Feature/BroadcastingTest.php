<?php

use App\Events\AdminMessageSent;
use App\Events\ChangeRequestReviewed;
use App\Events\KostMessageSent;
use App\Livewire\Admin\AdminMessages;
use App\Livewire\Admin\ModerationDashboard;
use App\Livewire\Contact\AdminChat;
use App\Livewire\Dashboard\OwnerChat;
use App\Livewire\Dashboard\OwnerDashboard;
use App\Livewire\Dashboard\SeekerChat;
use App\Livewire\NavbarBadges;
use App\Models\AdminConversation;
use App\Models\AdminMessage;
use App\Models\Kost;
use App\Models\KostChangeRequest;
use App\Models\KostConversation;
use App\Models\KostMessage;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Livewire;

function broadcastingOwner(): User
{
    return User::factory()->create(['role' => 'owner']);
}

function broadcastingSeeker(): User
{
    return User::factory()->create(['role' => 'user']);
}

function broadcastingKost(User $owner): Kost
{
    $name = 'Kost Broadcast '.Str::random(6);

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

function broadcastingChangeRequest(Kost $kost, User $owner, string $status): KostChangeRequest
{
    return KostChangeRequest::create([
        'kost_id' => $kost->id,
        'user_id' => $owner->id,
        'name' => $kost->name,
        'gender_type' => 'campur',
        'district' => 'Andir',
        'address' => $kost->address,
        'latitude' => $kost->latitude,
        'longitude' => $kost->longitude,
        'status' => $status,
        'review_note' => 'Catatan review.',
    ]);
}

function enableReverbChannelAuth(): void
{
    config()->set('broadcasting.default', 'reverb');
    config()->set('broadcasting.connections.reverb', [
        'driver' => 'reverb',
        'key' => 'test-key',
        'secret' => 'test-secret',
        'app_id' => 'test-app',
        'options' => [
            'host' => '127.0.0.1',
            'port' => 8080,
            'scheme' => 'http',
            'useTLS' => false,
        ],
        'client_options' => [],
    ]);

    // Saat aplikasi boot, routes/channels.php didaftarkan pada driver default
    // (biasanya "null" di lingkungan pengujian). Di sini kita paksa driver
    // reverb yang baru di-resolve untuk memuat ulang kanal-kanal tersebut.
    Broadcast::forgetDrivers();
    require base_path('routes/channels.php');
}

it('authorizes a user to its own private channel', function () {
    enableReverbChannelAuth();

    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/broadcasting/auth', [
            'channel_name' => 'private-App.Models.User.'.$user->id,
            'socket_id' => '12345.6789',
        ])
        ->assertOk()
        ->assertJsonStructure(['auth']);
});

it('rejects subscribing to another users private channel', function () {
    enableReverbChannelAuth();

    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/broadcasting/auth', [
            'channel_name' => 'private-App.Models.User.'.$other->id,
            'socket_id' => '12345.6789',
        ])
        ->assertForbidden();
});

it('authorizes only admins to the admin inbox channel', function () {
    enableReverbChannelAuth();

    $admin = User::factory()->create(['role' => 'admin']);
    $owner = User::factory()->create(['role' => 'owner']);

    $this->actingAs($admin)
        ->postJson('/broadcasting/auth', [
            'channel_name' => 'private-admin.inbox',
            'socket_id' => '12345.6789',
        ])
        ->assertOk()
        ->assertJsonStructure(['auth']);

    $this->actingAs($owner)
        ->postJson('/broadcasting/auth', [
            'channel_name' => 'private-admin.inbox',
            'socket_id' => '12345.6789',
        ])
        ->assertForbidden();
});

it('broadcasts the change review verdict to the owners private channel', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $owner = broadcastingOwner();
    $kost = broadcastingKost($owner);
    $request = broadcastingChangeRequest($kost, $owner, KostChangeRequest::STATUS_PENDING);

    Event::fake([ChangeRequestReviewed::class]);
    Mail::fake();

    Livewire::actingAs($admin)
        ->test(ModerationDashboard::class)
        ->call('approveChange', $request->id);

    Event::assertDispatched(ChangeRequestReviewed::class, function (ChangeRequestReviewed $event) use ($owner) {
        return $event->broadcastOn()[0]->name === 'private-App.Models.User.'.$owner->id
            && $event->broadcastWith()['status'] === KostChangeRequest::STATUS_APPROVED;
    });
});

it('broadcasts the change review rejection to the owners private channel', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $owner = broadcastingOwner();
    $kost = broadcastingKost($owner);
    $request = broadcastingChangeRequest($kost, $owner, KostChangeRequest::STATUS_PENDING);

    Event::fake([ChangeRequestReviewed::class]);
    Mail::fake();

    Livewire::actingAs($admin)
        ->test(ModerationDashboard::class)
        ->call('rejectChange', $request->id, 'Alamat tidak valid.');

    Event::assertDispatched(ChangeRequestReviewed::class, function (ChangeRequestReviewed $event) use ($owner) {
        return $event->broadcastOn()[0]->name === 'private-App.Models.User.'.$owner->id
            && $event->broadcastWith()['status'] === KostChangeRequest::STATUS_REJECTED
            && str_contains($event->broadcastWith()['message'], 'ditolak');
    });
});

it('broadcasts a seeker message to the owner channel', function () {
    $owner = broadcastingOwner();
    $seeker = broadcastingSeeker();
    $kost = broadcastingKost($owner);

    $conversation = KostConversation::create([
        'kost_id' => $kost->id,
        'seeker_id' => $seeker->id,
        'status' => KostConversation::STATUS_OPEN,
    ]);

    Event::fake([KostMessageSent::class]);

    Livewire::actingAs($seeker)
        ->test(SeekerChat::class, ['selectedConversationId' => $conversation->id])
        ->set('newBody', 'Apakah masih ada kamar kosong?')
        ->call('sendMessage');

    Event::assertDispatched(KostMessageSent::class, function (KostMessageSent $event) use ($owner) {
        return $event->broadcastOn()[0]->name === 'private-App.Models.User.'.$owner->id;
    });
});

it('broadcasts an owner reply to the seeker channel', function () {
    $owner = broadcastingOwner();
    $seeker = broadcastingSeeker();
    $kost = broadcastingKost($owner);

    $conversation = KostConversation::create([
        'kost_id' => $kost->id,
        'seeker_id' => $seeker->id,
        'status' => KostConversation::STATUS_OPEN,
    ]);

    Event::fake([KostMessageSent::class]);

    Livewire::actingAs($owner)
        ->test(OwnerChat::class, ['selectedConversationId' => $conversation->id])
        ->set('newBody', 'Masih ada satu kamar.')
        ->call('sendMessage');

    Event::assertDispatched(KostMessageSent::class, function (KostMessageSent $event) use ($seeker) {
        return $event->broadcastOn()[0]->name === 'private-App.Models.User.'.$seeker->id;
    });
});

it('broadcasts a user message to the admin inbox channel', function () {
    $user = broadcastingSeeker();

    Event::fake([AdminMessageSent::class]);

    Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->call('openCompose')
        ->set('category', 'pertanyaan')
        ->set('newBody', 'Halo, apakah ada promo?')
        ->call('sendNewConversation');

    Event::assertDispatched(AdminMessageSent::class, function (AdminMessageSent $event) {
        return $event->broadcastOn()[0]->name === 'private-admin.inbox';
    });
});

it('broadcasts an admin reply to the users private channel', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = broadcastingSeeker();

    $conversation = AdminConversation::create([
        'user_id' => $user->id,
        'sender_role' => 'user',
        'category' => 'komplain',
        'status' => 'open',
    ]);

    Event::fake([AdminMessageSent::class]);

    Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->call('openConversation', $conversation->id)
        ->set('replyBody', 'Baik, akan segera kami proses.')
        ->call('replyConversation');

    Event::assertDispatched(AdminMessageSent::class, function (AdminMessageSent $event) use ($user) {
        return $event->broadcastOn()[0]->name === 'private-App.Models.User.'.$user->id;
    });
});

it('shows a toast when a change review broadcast arrives on the owner dashboard', function () {
    $owner = broadcastingOwner();

    Livewire::actingAs($owner)
        ->test(OwnerDashboard::class)
        ->call('handleChangeReviewed', [
            'status' => KostChangeRequest::STATUS_APPROVED,
            'message' => 'Pengajuan perubahan disetujui.',
        ])
        ->assertDispatched('show-toast', message: 'Pengajuan perubahan disetujui.', type: 'success');

    Livewire::actingAs($owner)
        ->test(OwnerDashboard::class)
        ->call('handleChangeReviewed', [
            'status' => KostChangeRequest::STATUS_REJECTED,
            'message' => 'Pengajuan perubahan ditolak.',
        ])
        ->assertDispatched('show-toast', message: 'Pengajuan perubahan ditolak.', type: 'error');
});

it('marks an arriving admin message as read when its conversation is open', function () {
    $user = broadcastingSeeker();

    $conversation = AdminConversation::create([
        'user_id' => $user->id,
        'sender_role' => 'user',
        'category' => 'pertanyaan',
        'status' => 'open',
    ]);

    $component = Livewire::actingAs($user)
        ->test(AdminChat::class)
        ->set('selectedConversationId', $conversation->id);

    $adminMessage = AdminMessage::create([
        'conversation_id' => $conversation->id,
        'sender_type' => 'admin',
        'sender_id' => User::factory()->create(['role' => 'admin'])->id,
        'body' => 'Balasan admin.',
    ]);

    $component->call('handleIncomingMessage', [
        'conversation_id' => $conversation->id,
        'sender_type' => 'admin',
        'message_id' => $adminMessage->id,
    ]);

    $this->assertNotNull($adminMessage->fresh()->read_at);
});

it('marks an arriving user message as read on the admin inbox', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = broadcastingSeeker();

    $conversation = AdminConversation::create([
        'user_id' => $user->id,
        'sender_role' => 'user',
        'category' => 'masukan',
        'status' => 'open',
        'awaiting_reply_at' => now(),
    ]);

    $component = Livewire::actingAs($admin)
        ->test(AdminMessages::class)
        ->set('selectedConversationId', $conversation->id);

    $userMessage = AdminMessage::create([
        'conversation_id' => $conversation->id,
        'sender_type' => 'user',
        'sender_id' => $user->id,
        'body' => 'Pesan baru dari user.',
    ]);

    $component->call('handleInboxUpdate', [
        'conversation_id' => $conversation->id,
        'sender_type' => 'user',
        'message_id' => $userMessage->id,
    ]);

    $this->assertNotNull($userMessage->fresh()->read_at);
});

it('refreshes navbar badges with current unread counts', function () {
    $owner = broadcastingOwner();
    $seeker = broadcastingSeeker();
    $kost = broadcastingKost($owner);

    $conversation = KostConversation::create([
        'kost_id' => $kost->id,
        'seeker_id' => $seeker->id,
        'status' => KostConversation::STATUS_OPEN,
    ]);

    KostMessage::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $seeker->id,
        'body' => 'Pesan belum dibaca owner.',
    ]);

    Livewire::actingAs($owner)
        ->test(NavbarBadges::class)
        ->call('refresh')
        ->assertDispatched('badges-updated', chat: 1, adminReplies: 0, adminUnanswered: 0);
});

it('exposes the correct channels and payload for the change review event', function () {
    $owner = broadcastingOwner();
    $kost = broadcastingKost($owner);
    $request = broadcastingChangeRequest($kost, $owner, KostChangeRequest::STATUS_REJECTED);

    $event = new ChangeRequestReviewed($request);

    expect($event->broadcastOn())
        ->toContainEqual(new PrivateChannel('App.Models.User.'.$owner->id))
        ->and($event->broadcastAs())->toBe('change.request.reviewed')
        ->and($event->broadcastWith())
        ->toMatchArray([
            'status' => KostChangeRequest::STATUS_REJECTED,
            'kost_id' => $kost->id,
            'kost_name' => $kost->name,
        ]);
});
