<?php

use App\Livewire\Profile\Index;
use App\Models\Kost;
use App\Models\KostConversation;
use App\Models\KostImage;
use App\Models\KostMessage;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
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

it('shows chat history and stats for a pencari kost', function () {
    $user = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = profileTestKost($owner);

    $conversation = KostConversation::create([
        'kost_id' => $kost->id,
        'seeker_id' => $user->id,
        'status' => KostConversation::STATUS_OPEN,
    ]);
    KostMessage::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $user->id,
        'body' => 'Apakah masih ada kamar?',
    ]);

    $this->actingAs($user)
        ->get('/profil')
        ->assertOk()
        ->assertSee($kost->name)
        ->assertSee('Riwayat Obrolan Saya');
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

it('sends a verification notification and resets verification when the email is changed', function () {
    $user = User::factory()->create(['role' => 'user']);
    Notification::fake();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('email', 'baru@example.com')
        ->call('updateProfile')
        ->assertHasNoErrors();

    expect($user->fresh()->email_verified_at)->toBeNull();

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('does not send a verification notification when the email is unchanged', function () {
    $user = User::factory()->create(['role' => 'user']);
    Notification::fake();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('name', 'Nama Baru')
        ->set('email', $user->email)
        ->call('updateProfile')
        ->assertHasNoErrors();

    Notification::assertNothingSent();
});

it('prevents an admin from changing their email', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Notification::fake();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('name', 'Admin Baru')
        ->set('email', 'admin-hacked@example.com')
        ->call('updateProfile')
        ->assertHasNoErrors();

    expect($admin->fresh()->email)->toBe($admin->email);
    expect($admin->fresh()->name)->toBe('Admin Baru');

    Notification::assertNothingSent();
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

it('rejects a name that is too short', function () {
    $user = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('name', 'a')
        ->call('updateProfile')
        ->assertHasErrors(['name' => 'min']);

    expect($user->fresh()->name)->not->toBe('a');
});

it('rejects a name containing invalid characters', function () {
    $user = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('name', 'ldhjdkhsdhgz;j')
        ->call('updateProfile')
        ->assertHasErrors(['name' => 'regex']);

    expect($user->fresh()->name)->not->toBe('ldhjdkhsdhgz;j');
});

it('accepts a valid unicode name', function () {
    $user = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('name', 'Agus Setiawan')
        ->call('updateProfile')
        ->assertHasNoErrors();

    expect($user->fresh()->name)->toBe('Agus Setiawan');
});

it('squishes surrounding whitespace from the name', function () {
    $user = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('name', '  Agus   Setiawan  ')
        ->call('updateProfile')
        ->assertHasNoErrors();

    expect($user->fresh()->name)->toBe('Agus Setiawan');
});

it('shows the identity verification card only to owners', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $pencari = User::factory()->create(['role' => 'user']);

    $this->actingAs($owner)
        ->get('/profil')
        ->assertOk()
        ->assertSee('Verifikasi Identitas (KTP)');

    $this->actingAs($pencari)
        ->get('/profil')
        ->assertOk()
        ->assertDontSee('Verifikasi Identitas (KTP)');
});

it('lets an owner upload an identity KTP document', function () {
    Storage::fake('verification_docs');
    $owner = User::factory()->create(['role' => 'owner']);

    Livewire::actingAs($owner)
        ->test(Index::class)
        ->set('identity_doc', UploadedFile::fake()->image('ktp.jpg'))
        ->assertHasNoErrors(['identity_doc'])
        ->assertDispatched('show-toast');

    $owner->refresh();
    expect($owner->identity_verification_status)->toBe('pending');
    expect($owner->identity_doc_path)->not->toBeNull();
    expect(Storage::disk('verification_docs')->exists($owner->identity_doc_path))->toBeTrue();
});

it('lets an owner re-upload identity KTP and clears the rejection note', function () {
    Storage::fake('verification_docs');
    $oldPath = 'verification-docs/identity/old-ktp.jpg';
    Storage::disk('verification_docs')->put($oldPath, 'old');

    $owner = User::factory()->create([
        'role' => 'owner',
        'identity_doc_path' => $oldPath,
        'identity_verification_status' => 'rejected',
        'identity_rejection_note' => 'Foto KTP buram, tidak terbaca.',
    ]);

    Livewire::actingAs($owner)
        ->test(Index::class)
        ->set('identity_doc', UploadedFile::fake()->image('ktp-baru.jpg'));

    $owner->refresh();
    expect($owner->identity_verification_status)->toBe('pending');
    expect($owner->identity_rejection_note)->toBeNull();
    expect($owner->identity_doc_path)->not->toBe($oldPath);
    expect(Storage::disk('verification_docs')->exists($oldPath))->toBeFalse();
});

it('does not let a pencari kost upload an identity KTP document', function () {
    Storage::fake('verification_docs');
    $user = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('identity_doc', UploadedFile::fake()->image('ktp.jpg'));

    $user->refresh();
    expect($user->identity_verification_status)->toBe('unverified');
    expect($user->identity_doc_path)->toBeNull();
});

it('lets an owner delete their identity KTP document', function () {
    Storage::fake('verification_docs');
    $path = 'verification-docs/identity/ktp.jpg';
    Storage::disk('verification_docs')->put($path, 'bytes');

    $owner = User::factory()->create([
        'role' => 'owner',
        'identity_doc_path' => $path,
        'identity_verification_status' => 'verified',
        'identity_verified_at' => now(),
    ]);

    Livewire::actingAs($owner)
        ->test(Index::class)
        ->call('deleteIdentityDocument')
        ->assertDispatched('show-toast');

    $owner->refresh();
    expect($owner->identity_verification_status)->toBe('unverified');
    expect($owner->identity_doc_path)->toBeNull();
    expect($owner->identity_verified_at)->toBeNull();
    expect($owner->identity_rejection_note)->toBeNull();
    expect(Storage::disk('verification_docs')->exists($path))->toBeFalse();
});

it('does not let a pencari kost delete an identity KTP document', function () {
    Storage::fake('verification_docs');
    $path = 'verification-docs/identity/ktp.jpg';
    Storage::disk('verification_docs')->put($path, 'bytes');

    $user = User::factory()->create(['role' => 'user']);
    $user->forceFill([
        'identity_doc_path' => $path,
        'identity_verification_status' => 'pending',
    ])->save();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deleteIdentityDocument');

    $user->refresh();
    expect($user->identity_doc_path)->toBe($path);
    expect($user->identity_verification_status)->toBe('pending');
    expect(Storage::disk('verification_docs')->exists($path))->toBeTrue();
});

it('shows the delete account section to a pencari kost on the profile page', function () {
    $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);

    $this->actingAs($user)
        ->get('/profil')
        ->assertOk()
        ->assertSee('Hapus Akun')
        ->assertSee('foto profil dan riwayat pesan')
        ->assertSee('open-delete-account-modal')
        ->assertDontSee('KTP')
        ->assertDontSee('bukti kepemilikan');
});

it('shows the delete account section to an owner on the profile page', function () {
    $owner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);

    $this->actingAs($owner)
        ->get('/profil')
        ->assertOk()
        ->assertSee('Hapus Akun')
        ->assertSee('KTP')
        ->assertSee('bukti kepemilikan');
});

it('hides the delete account section from an admin', function () {
    $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

    $this->actingAs($admin)
        ->get('/profil')
        ->assertOk()
        ->assertDontSee('Hapus Akun');
});

it('lets an owner delete their account from the profile page and purges all files', function () {
    Storage::fake('verification_docs');
    Storage::fake('local');

    $owner = User::factory()->create([
        'role' => 'owner',
        'email_verified_at' => now(),
        'avatar' => 'avatars/owner.jpg',
        'identity_doc_path' => 'verification-docs/identity/ktp.jpg',
    ]);

    Storage::disk('local')->put('avatars/owner.jpg', 'bytes');
    Storage::disk('verification_docs')->put('verification-docs/identity/ktp.jpg', 'bytes');
    Storage::disk('verification_docs')->put('verification-docs/ownership/pbb.jpg', 'bytes');

    $kost = profileTestKost($owner);
    $kost->update(['ownership_doc_path' => 'verification-docs/ownership/pbb.jpg']);

    KostImage::create([
        'kost_id' => $kost->id,
        'image_path' => 'kosts/photo.jpg',
        'is_primary' => true,
    ]);
    Storage::disk('local')->put('kosts/photo.jpg', 'bytes');

    Livewire::actingAs($owner)
        ->test(Index::class)
        ->set('deletePassword', 'password')
        ->call('deleteAccount')
        ->assertHasNoErrors();

    expect($owner->fresh())->toBeNull()
        ->and(Kost::withTrashed()->count())->toBe(0);

    Storage::disk('local')->assertMissing('avatars/owner.jpg');
    Storage::disk('local')->assertMissing('kosts/photo.jpg');
    Storage::disk('verification_docs')->assertMissing('verification-docs/identity/ktp.jpg');
    Storage::disk('verification_docs')->assertMissing('verification-docs/ownership/pbb.jpg');
});

it('keeps the account and files when the wrong password is given', function () {
    Storage::fake('verification_docs');
    Storage::fake('local');

    $owner = User::factory()->create([
        'role' => 'owner',
        'email_verified_at' => now(),
        'avatar' => 'avatars/owner.jpg',
    ]);
    Storage::disk('local')->put('avatars/owner.jpg', 'bytes');
    profileTestKost($owner);

    Livewire::actingAs($owner)
        ->test(Index::class)
        ->set('deletePassword', 'wrong-password')
        ->call('deleteAccount')
        ->assertHasErrors(['deletePassword']);

    expect($owner->fresh())->not->toBeNull()
        ->and(Kost::count())->toBe(1);

    Storage::disk('local')->assertExists('avatars/owner.jpg');
});

it('does not let an admin delete their account from the profile page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('deletePassword', 'password')
        ->call('deleteAccount')
        ->assertHasNoErrors();

    expect($admin->fresh())->not->toBeNull();
});

it('deleting an account purges files of soft-deleted kosts too', function () {
    Storage::fake('verification_docs');
    Storage::fake('local');

    $owner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);

    $kost = profileTestKost($owner);
    $kost->update(['ownership_doc_path' => 'verification-docs/ownership/pbb.jpg']);
    KostImage::create([
        'kost_id' => $kost->id,
        'image_path' => 'kosts/softdeleted.jpg',
        'is_primary' => true,
    ]);

    Storage::disk('local')->put('kosts/softdeleted.jpg', 'bytes');
    Storage::disk('verification_docs')->put('verification-docs/ownership/pbb.jpg', 'bytes');

    $kost->delete();

    Livewire::actingAs($owner)
        ->test(Index::class)
        ->set('deletePassword', 'password')
        ->call('deleteAccount')
        ->assertHasNoErrors();

    expect(Kost::withTrashed()->count())->toBe(0);

    Storage::disk('local')->assertMissing('kosts/softdeleted.jpg');
    Storage::disk('verification_docs')->assertMissing('verification-docs/ownership/pbb.jpg');
});

it('deleting an account of a non-owner purges the avatar file', function () {
    Storage::fake('local');

    $user = User::factory()->create([
        'email_verified_at' => now(),
        'avatar' => 'avatars/searcher.jpg',
    ]);
    Storage::disk('local')->put('avatars/searcher.jpg', 'bytes');

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('deletePassword', 'password')
        ->call('deleteAccount')
        ->assertHasNoErrors();

    expect($user->fresh())->toBeNull();

    Storage::disk('local')->assertMissing('avatars/searcher.jpg');
});

it('redirects legacy settings routes to /profil', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)->get('/settings')->assertRedirect('/profil');
    $this->actingAs($user)->get('/settings/profile')->assertRedirect('/profil');
    $this->actingAs($user)->get('/settings/appearance')->assertRedirect('/profil');
    $this->actingAs($user)->get('/settings/security')->assertRedirect('/profil');
});

it('points well-known passkey endpoints to profile.show', function () {
    $response = $this->get(route('well-known.passkeys'));

    $response->assertOk()
        ->assertJson([
            'enroll' => route('profile.show'),
            'manage' => route('profile.show'),
        ]);
});
