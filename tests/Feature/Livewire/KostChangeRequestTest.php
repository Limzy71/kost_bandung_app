<?php

use App\Livewire\Admin\ModerationDashboard;
use App\Livewire\Dashboard\EditKost;
use App\Livewire\Dashboard\OwnerDashboard;
use App\Models\Kost;
use App\Models\KostChangeRequest;
use App\Models\KostImage;
use App\Models\User;
use App\Notifications\KostChangeReviewed;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Livewire;

function makeChangeRequestOwnerKost(User $user, int $imageCount = 4): Kost
{
    $kost = Kost::create([
        'user_id' => $user->id,
        'name' => 'Kost Change Request '.$user->id,
        'slug' => 'kost-change-request-'.$user->id.'-'.Str::random(6),
        'description' => 'Deskripsi kost yang cukup panjang minimal sepuluh',
        'gender_type' => 'campur',
        'price_monthly' => 1000000,
        'rent_period' => 'monthly',
        'address' => 'Jl. Lama No. 1',
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
            'image_path' => 'kosts/test-'.$kost->id.'-'.$i.'.jpg',
            'is_primary' => $i === 0,
        ]);
    }

    return $kost;
}

it('creates a pending change request when an owner edits core fields of a published kost', function () {
    Mail::fake();
    $owner = User::factory()->create();
    $kost = makeChangeRequestOwnerKost($owner);

    $this->actingAs($owner);

    Livewire::test(EditKost::class, ['kost' => $kost])
        ->set('name', 'Kost Andir Baru')
        ->set('address', 'Jl. Baru No. 99')
        ->call('save')
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('status');

    $request = KostChangeRequest::where('kost_id', $kost->id)->first();

    expect($request)->not->toBeNull();
    expect($request->status)->toBe(KostChangeRequest::STATUS_PENDING);
    expect($request->name)->toBe('Kost Andir Baru');
    expect($request->address)->toBe('Jl. Baru No. 99');

    expect($kost->fresh()->name)->toBe('Kost Change Request '.$owner->id);
});

it('does not create a change request when core fields are unchanged', function () {
    $owner = User::factory()->create();
    $kost = makeChangeRequestOwnerKost($owner);

    $this->actingAs($owner);

    Livewire::test(EditKost::class, ['kost' => $kost])
        ->call('save')
        ->assertRedirect(route('dashboard'));

    expect(KostChangeRequest::where('kost_id', $kost->id)->count())->toBe(0);
});

it('does not create a change request when coordinates are only reformatted, not changed', function () {
    $owner = User::factory()->create();
    $kost = makeChangeRequestOwnerKost($owner);

    $this->actingAs($owner);

    Livewire::test(EditKost::class, ['kost' => $kost])
        ->set('latitude', '-6.9180')
        ->set('longitude', '107.5840')
        ->call('save')
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('status');

    expect(KostChangeRequest::where('kost_id', $kost->id)->count())->toBe(0);
});

it('blocks a second change request while one is still pending', function () {
    $owner = User::factory()->create();
    $kost = makeChangeRequestOwnerKost($owner);

    KostChangeRequest::create([
        'kost_id' => $kost->id,
        'user_id' => $owner->id,
        'name' => 'Nama Proposal',
        'gender_type' => 'campur',
        'district' => 'Andir',
        'address' => 'Jl. Proposal',
        'latitude' => -6.918,
        'longitude' => 107.584,
    ]);

    $this->actingAs($owner);

    Livewire::test(EditKost::class, ['kost' => $kost])
        ->set('name', 'Nama Lain')
        ->call('save')
        ->assertHasErrors(['name']);

    expect(KostChangeRequest::where('kost_id', $kost->id)->count())->toBe(1);
});

it('blocks a change request created after the form was mounted', function () {
    $owner = User::factory()->create();
    $kost = makeChangeRequestOwnerKost($owner);

    $this->actingAs($owner);

    $component = Livewire::test(EditKost::class, ['kost' => $kost])
        ->assertSet('hasPendingChangeRequest', false);

    KostChangeRequest::create([
        'kost_id' => $kost->id,
        'user_id' => $owner->id,
        'name' => 'Nama Proposal',
        'gender_type' => 'campur',
        'district' => 'Andir',
        'address' => 'Jl. Proposal',
        'latitude' => -6.918,
        'longitude' => 107.584,
    ]);

    $component->set('name', 'Nama Lain')
        ->call('save')
        ->assertHasErrors(['name']);

    expect(KostChangeRequest::where('kost_id', $kost->id)->count())->toBe(1);
    expect($kost->fresh()->name)->toBe('Kost Change Request '.$owner->id);
});

it('does not apply a change request when the kost has been deleted', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $kost = makeChangeRequestOwnerKost($owner);

    $request = KostChangeRequest::create([
        'kost_id' => $kost->id,
        'user_id' => $owner->id,
        'name' => 'Kost Nama Baru',
        'gender_type' => 'campur',
        'district' => 'Andir',
        'address' => 'Jl. Baru No. 77',
        'latitude' => -6.918,
        'longitude' => 107.584,
    ]);

    $kost->delete();

    $this->actingAs($admin);

    Livewire::test(ModerationDashboard::class)
        ->call('approveChange', $request->id)
        ->assertHasNoErrors();

    $request->refresh();

    expect($request->status)->toBe(KostChangeRequest::STATUS_PENDING);
});

it('lets an admin approve a change request, applies the data, and notifies the owner', function () {
    Mail::fake();
    $owner = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $kost = makeChangeRequestOwnerKost($owner);

    $request = KostChangeRequest::create([
        'kost_id' => $kost->id,
        'user_id' => $owner->id,
        'name' => 'Kost Nama Baru',
        'gender_type' => 'putri',
        'district' => 'Coblong',
        'address' => 'Jl. Baru No. 77',
        'latitude' => -6.902,
        'longitude' => 107.620,
    ]);

    $this->actingAs($admin);

    Livewire::test(ModerationDashboard::class)
        ->call('approveChange', $request->id);

    $kost->refresh();
    $request->refresh();

    expect($kost->name)->toBe('Kost Nama Baru');
    expect($kost->gender_type)->toBe('putri');
    expect($kost->district)->toBe('Coblong');
    expect($kost->address)->toBe('Jl. Baru No. 77');
    expect($kost->slug)->toBe('kost-nama-baru');

    expect($request->status)->toBe(KostChangeRequest::STATUS_APPROVED);
    expect($request->reviewed_at)->not->toBeNull();

    expect($owner->fresh()->unreadNotifications()->count())->toBe(1);
    expect($owner->fresh()->unreadNotifications()->first()->type)->toBe(KostChangeReviewed::class);
});

it('lets an admin reject a change request and notifies the owner with the reason', function () {
    Mail::fake();
    $owner = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $kost = makeChangeRequestOwnerKost($owner);

    $request = KostChangeRequest::create([
        'kost_id' => $kost->id,
        'user_id' => $owner->id,
        'name' => 'Kost Nama Baru',
        'gender_type' => 'campur',
        'district' => 'Andir',
        'address' => 'Jl. Baru No. 77',
        'latitude' => -6.918,
        'longitude' => 107.584,
    ]);

    $this->actingAs($admin);

    Livewire::test(ModerationDashboard::class)
        ->call('rejectChange', $request->id, 'Alamat tidak lengkap.');

    $request->refresh();

    expect($request->status)->toBe(KostChangeRequest::STATUS_REJECTED);
    expect($request->review_note)->toBe('Alamat tidak lengkap.');

    expect($kost->fresh()->name)->toBe('Kost Change Request '.$owner->id);

    $notification = $owner->fresh()->unreadNotifications()->first();

    expect($notification)->not->toBeNull();
    expect($notification->data['status'])->toBe(KostChangeRequest::STATUS_REJECTED);
    expect($notification->data['review_note'])->toBe('Alamat tidak lengkap.');
});

it('shows the reviewed change notification on the owner dashboard and marks it read', function () {
    $owner = User::factory()->create();
    $kost = makeChangeRequestOwnerKost($owner);

    $owner->notify(new KostChangeReviewed(KostChangeRequest::create([
        'kost_id' => $kost->id,
        'user_id' => $owner->id,
        'name' => 'Kost Nama Baru',
        'gender_type' => 'campur',
        'district' => 'Andir',
        'address' => 'Jl. Baru No. 77',
        'latitude' => -6.918,
        'longitude' => 107.584,
        'status' => KostChangeRequest::STATUS_APPROVED,
        'reviewed_at' => now(),
    ])));

    $this->actingAs($owner);

    Livewire::test(OwnerDashboard::class)
        ->assertViewHas('changeNotifications', fn ($notifications) => $notifications->count() === 1)
        ->call('markChangeNotificationsRead');

    expect($owner->fresh()->unreadNotifications()->count())->toBe(0);
});
