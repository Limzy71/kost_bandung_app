<?php

use App\Livewire\Settings\Profile;
use App\Models\Kost;
use App\Models\KostImage;
use App\Models\KostPrice;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

function profileDeleteKost(User $owner, string $status = 'published'): Kost
{
    $name = 'Kost Hapus Akun '.Str::random(6);

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
        'ownership_doc_path' => 'verification-docs/ownership/pbb.jpg',
    ]);
}

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create(['email_verified_at' => now()]));

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('sends a verification notification when the email is changed', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Notification::fake();

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->fresh()->email_verified_at)->toBeNull();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('prevents an admin from changing their email', function () {
    $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    Notification::fake();

    $this->actingAs($admin);

    Livewire::test(Profile::class)
        ->set('name', 'Test Admin')
        ->set('email', 'admin-hacked@example.com')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($admin->fresh()->email)->toBe($admin->email);
    expect($admin->fresh()->name)->toEqual('Test Admin');

    Notification::assertNothingSent();
});

test('user can delete their account', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});

test('rejects a name that is too short', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('name', 'a')
        ->call('updateProfileInformation')
        ->assertHasErrors(['name' => 'min']);

    expect($user->fresh()->name)->not->toBe('a');
});

test('rejects a name containing invalid characters', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('name', 'ldhjdkhsdhgz;j')
        ->call('updateProfileInformation')
        ->assertHasErrors(['name' => 'regex']);

    expect($user->fresh()->name)->not->toBe('ldhjdkhsdhgz;j');
});

test('accepts a valid unicode name', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('name', 'Agus Setiawan')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->fresh()->name)->toBe('Agus Setiawan');
});

test('deleting an account purges all uploaded files and related rows', function () {
    Storage::fake('verification_docs');
    Storage::fake('local');

    $owner = User::factory()->create([
        'role' => 'owner',
        'email_verified_at' => now(),
        'avatar' => 'avatars/owner.jpg',
        'identity_doc_path' => 'verification-docs/identity/ktp.jpg',
    ]);

    Storage::disk('local')->put('avatars/owner.jpg', 'avatar-bytes');
    Storage::disk('verification_docs')->put('verification-docs/identity/ktp.jpg', 'ktp-bytes');
    Storage::disk('verification_docs')->put('verification-docs/ownership/pbb.jpg', 'pbb-bytes');

    $kost = profileDeleteKost($owner);

    KostImage::create([
        'kost_id' => $kost->id,
        'image_path' => 'kosts/photo.jpg',
        'is_primary' => true,
    ]);
    Storage::disk('local')->put('kosts/photo.jpg', 'photo-bytes');

    KostPrice::create(['kost_id' => $kost->id, 'period' => 'weekly', 'price' => 500000]);

    Livewire::actingAs($owner)
        ->test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors();

    expect($owner->fresh())->toBeNull()
        ->and(Kost::withTrashed()->count())->toBe(0)
        ->and(KostImage::count())->toBe(0)
        ->and(KostPrice::count())->toBe(0);

    Storage::disk('local')->assertMissing('avatars/owner.jpg');
    Storage::disk('local')->assertMissing('kosts/photo.jpg');
    Storage::disk('verification_docs')->assertMissing('verification-docs/identity/ktp.jpg');
    Storage::disk('verification_docs')->assertMissing('verification-docs/ownership/pbb.jpg');
});

test('deleting an account purges files of soft-deleted kosts too', function () {
    Storage::fake('verification_docs');
    Storage::fake('local');

    $owner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);

    $kost = profileDeleteKost($owner);
    KostImage::create([
        'kost_id' => $kost->id,
        'image_path' => 'kosts/softdeleted.jpg',
        'is_primary' => true,
    ]);

    Storage::disk('local')->put('kosts/softdeleted.jpg', 'bytes');
    Storage::disk('verification_docs')->put('verification-docs/ownership/pbb.jpg', 'bytes');

    $kost->delete();

    Livewire::actingAs($owner)
        ->test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors();

    expect(Kost::withTrashed()->count())->toBe(0);

    Storage::disk('local')->assertMissing('kosts/softdeleted.jpg');
    Storage::disk('verification_docs')->assertMissing('verification-docs/ownership/pbb.jpg');
});

test('deleting an account of a non-owner purges the avatar file', function () {
    Storage::fake('local');

    $user = User::factory()->create([
        'email_verified_at' => now(),
        'avatar' => 'avatars/searcher.jpg',
    ]);
    Storage::disk('local')->put('avatars/searcher.jpg', 'bytes');

    Livewire::actingAs($user)
        ->test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors();

    expect($user->fresh())->toBeNull();

    Storage::disk('local')->assertMissing('avatars/searcher.jpg');
});

test('files are kept when account deletion is cancelled by a wrong password', function () {
    Storage::fake('verification_docs');
    Storage::fake('local');

    $owner = User::factory()->create([
        'role' => 'owner',
        'email_verified_at' => now(),
        'avatar' => 'avatars/owner.jpg',
        'identity_doc_path' => 'verification-docs/identity/ktp.jpg',
    ]);

    Storage::disk('local')->put('avatars/owner.jpg', 'avatar-bytes');
    Storage::disk('verification_docs')->put('verification-docs/identity/ktp.jpg', 'ktp-bytes');
    Storage::disk('verification_docs')->put('verification-docs/ownership/pbb.jpg', 'pbb-bytes');

    profileDeleteKost($owner);

    Livewire::actingAs($owner)
        ->test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser')
        ->assertHasErrors(['password']);

    expect($owner->fresh())->not->toBeNull()
        ->and(Kost::count())->toBe(1);

    Storage::disk('local')->assertExists('avatars/owner.jpg');
    Storage::disk('verification_docs')->assertExists('verification-docs/identity/ktp.jpg');
    Storage::disk('verification_docs')->assertExists('verification-docs/ownership/pbb.jpg');
});
