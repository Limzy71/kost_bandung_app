<?php

use App\Livewire\Admin\ModerationDashboard;
use App\Livewire\Dashboard\CreateKost;
use App\Livewire\KostSearch;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

function verifTestKost(User $user, string $name = 'Kost Uji'): Kost
{
    return Kost::create([
        'user_id' => $user->id,
        'name' => $name,
        'slug' => Str::slug($name).'-'.Str::random(6),
        'description' => 'Deskripsi kost minimal 10 karakter ya',
        'gender_type' => 'putra',
        'price_monthly' => 1500000,
        'rent_period' => 'monthly',
        'address' => 'Jalan Dago',
        'district' => 'Coblong',
        'latitude' => -6.8830,
        'longitude' => 107.6160,
        'is_available' => true,
        'status' => 'pending',
        'total_rooms' => 5,
        'available_rooms' => 3,
    ]);
}

function verifTestKostPayload(string $name = 'Kost Test'): array
{
    $image = UploadedFile::fake()->image('test.jpg');

    return [
        'name' => $name,
        'gender_type' => 'putra',
        'description' => 'Deskripsi kost minimal 10 karakter ya',
        'district' => 'Coblong',
        'address' => 'Jalan Dago',
        'price_monthly' => 1500000,
        'latitude' => -6.8830,
        'longitude' => 107.6160,
        'total_rooms' => 5,
        'available_rooms' => 5,
        'photos' => [$image, $image, $image, $image],
    ];
}

it('saves a kost without verification documents as unverified', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateKost::class)
        ->set(verifTestKostPayload())
        ->call('save')
        ->assertHasNoErrors(['ownership_doc', 'ownership_doc_type'])
        ->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->identity_verification_status)->toBe('unverified');
    expect($user->identity_doc_path)->toBeNull();

    $kost = Kost::where('name', 'Kost Test')->first();
    expect($kost)->not->toBeNull();
    expect($kost->ownership_verification_status)->toBe('unverified');
    expect($kost->ownership_doc_type)->toBeNull();
    expect($kost->ownership_doc_path)->toBeNull();
});

it('marks an uploaded ownership document as pending and stores its path', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateKost::class)
        ->set(verifTestKostPayload())
        ->set('ownership_doc', UploadedFile::fake()->image('pbb.jpg'))
        ->set('ownership_doc_type', 'pbb')
        ->call('save')
        ->assertHasNoErrors(['ownership_doc', 'ownership_doc_type'])
        ->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->identity_verification_status)->toBe('unverified');
    expect($user->identity_doc_path)->toBeNull();

    $kost = Kost::where('name', 'Kost Test')->first();
    expect($kost->ownership_verification_status)->toBe('pending');
    expect($kost->ownership_doc_type)->toBe('pbb');
    expect($kost->ownership_doc_path)->not->toBeNull();
    expect(Storage::disk(config('filesystems.default'))->exists($kost->ownership_doc_path))->toBeTrue();
});

it('requires ownership_doc_type when an ownership document is uploaded', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateKost::class)
        ->set(verifTestKostPayload())
        ->set('ownership_doc', UploadedFile::fake()->image('pbb.jpg'))
        ->set('ownership_doc_type', '')
        ->call('save')
        ->assertHasErrors(['ownership_doc_type']);
});

it('rejects legacy ownership document types', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateKost::class)
        ->set(verifTestKostPayload())
        ->set('ownership_doc', UploadedFile::fake()->image('shm.jpg'))
        ->set('ownership_doc_type', 'shm')
        ->call('save')
        ->assertHasErrors(['ownership_doc_type']);
});

it('allows admin to publish a kost without verification documents', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $kost = verifTestKost(User::factory()->create());

    Livewire::actingAs($admin)
        ->test(ModerationDashboard::class)
        ->call('approve', $kost->id)
        ->assertDispatched('show-toast');

    expect($kost->refresh()->status)->toBe('published');
    expect($kost->isOwnershipVerified())->toBeFalse();
    expect($kost->user->isIdentityVerified())->toBeFalse();
});

it('approves identity verification', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $user->identity_verification_status = 'pending';
    $user->identity_doc_path = 'verification-docs/identity/ktp.jpg';
    $user->save();

    Livewire::actingAs($admin)
        ->test(ModerationDashboard::class)
        ->call('approveIdentity', $user->id);

    $user->refresh();
    expect($user->identity_verification_status)->toBe('verified');
    expect($user->isIdentityVerified())->toBeTrue();
    expect($user->identity_verified_at)->not->toBeNull();
    expect($user->identity_rejection_note)->toBeNull();
});

it('approves ownership verification', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $kost = verifTestKost(User::factory()->create());
    $kost->ownership_verification_status = 'pending';
    $kost->ownership_doc_path = 'verification-docs/ownership/pbb.jpg';
    $kost->save();

    Livewire::actingAs($admin)
        ->test(ModerationDashboard::class)
        ->call('approveOwnership', $kost->id);

    expect($kost->refresh()->ownership_verification_status)->toBe('verified');
    expect($kost->isOwnershipVerified())->toBeTrue();
    expect($kost->ownership_verified_at)->not->toBeNull();
    expect($kost->ownership_rejection_note)->toBeNull();
});

it('considers a kost verified only when identity and ownership are both verified', function () {
    $owner = User::factory()->create();
    $owner->identity_verification_status = 'verified';
    $owner->save();

    $both = verifTestKost($owner, 'Kost Terverifikasi Penuh');
    $both->ownership_verification_status = 'verified';
    $both->save();

    $identityOnly = verifTestKost($owner, 'Kost Hanya Identitas');
    $ownershipOnly = verifTestKost(User::factory()->create(), 'Kost Hanya Kepemilikan');
    $ownershipOnly->ownership_verification_status = 'verified';
    $ownershipOnly->save();

    expect($both->isVerified())->toBeTrue();
    expect($identityOnly->isVerified())->toBeFalse();
    expect($ownershipOnly->isVerified())->toBeFalse();
});

it('records a rejection reason for identity and ownership documents', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $user = User::factory()->create();
    $user->identity_verification_status = 'pending';
    $user->save();

    $kost = verifTestKost(User::factory()->create());
    $kost->ownership_verification_status = 'pending';
    $kost->save();

    Livewire::actingAs($admin)
        ->test(ModerationDashboard::class)
        ->call('submitReject', 'identity', $user->id, 'KTP buram, tidak terbaca.')
        ->call('submitReject', 'ownership', $kost->id, 'Nama di dokumen berbeda dengan pemilik.');

    expect($user->refresh()->identity_verification_status)->toBe('rejected');
    expect($user->identity_rejection_note)->toBe('KTP buram, tidak terbaca.');

    expect($kost->refresh()->ownership_verification_status)->toBe('rejected');
    expect($kost->ownership_rejection_note)->toBe('Nama di dokumen berbeda dengan pemilik.');
});

it('restricts the verification document route to admins', function () {
    $user = User::factory()->create();
    $user->identity_doc_path = 'verification-docs/identity/ktp.jpg';
    $user->save();

    $this->get(route('admin.verification.document', ['kind' => 'identity', 'id' => $user->id]))
        ->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->get(route('admin.verification.document', ['kind' => 'identity', 'id' => $user->id]))
        ->assertForbidden();

    Storage::fake(config('filesystems.default'));
    Storage::disk(config('filesystems.default'))->put('verification-docs/identity/ktp.jpg', 'image-bytes');

    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)
        ->get(route('admin.verification.document', ['kind' => 'identity', 'id' => $user->id]))
        ->assertOk();
});

it('filters the search to verified-only kosts', function () {
    $verifiedOwner = User::factory()->create();
    $verifiedOwner->identity_verification_status = 'verified';
    $verifiedOwner->save();

    $verifiedKost = verifTestKost($verifiedOwner, 'Kost Terverifikasi Premium');
    $verifiedKost->status = 'published';
    $verifiedKost->ownership_verification_status = 'verified';
    $verifiedKost->save();

    $plainKost = verifTestKost(User::factory()->create(), 'Kost Biasa Tanpa Verifikasi');
    $plainKost->status = 'published';
    $plainKost->save();

    Livewire::test(KostSearch::class)
        ->set('verified_only', true)
        ->assertSee('Kost Terverifikasi Premium')
        ->assertDontSee('Kost Biasa Tanpa Verifikasi');
});
