<?php

use App\Livewire\Profile\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake(config('filesystems.default'));
});

it('lets a user upload a profile photo', function () {
    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $file = UploadedFile::fake()->image('avatar.jpg', 400, 400);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('avatarUpload', $file);

    $user->refresh();

    expect($user->avatar)->not->toBeNull();
    Storage::disk(config('filesystems.default'))->assertExists($user->avatar);
});

it('lets an owner upload and replace a profile photo, removing the old file', function () {
    $user = User::factory()->create([
        'role' => 'owner',
    ]);

    $firstFile = UploadedFile::fake()->image('first.png', 400, 400);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('avatarUpload', $firstFile);

    $user->refresh();
    $oldPath = $user->avatar;

    $secondFile = UploadedFile::fake()->image('second.jpg', 400, 400);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('avatarUpload', $secondFile);

    $user->refresh();

    expect($user->avatar)->not->toBeNull()
        ->and($user->avatar)->not->toBe($oldPath);

    Storage::disk(config('filesystems.default'))->assertMissing($oldPath);
    Storage::disk(config('filesystems.default'))->assertExists($user->avatar);
});

it('lets an admin delete a profile photo', function () {
    $fakePath = 'avatars/test-admin.jpg';
    Storage::disk(config('filesystems.default'))->put($fakePath, 'dummy content');

    $user = User::factory()->create([
        'role' => 'admin',
        'avatar' => $fakePath,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deleteAvatar');

    $user->refresh();

    expect($user->avatar)->toBeNull();
    Storage::disk(config('filesystems.default'))->assertMissing($fakePath);
});

it('rejects a file that is not an image', function () {
    $user = User::factory()->create();

    $file = UploadedFile::fake()->create('document.pdf', 3000);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('avatarUpload', $file)
        ->assertHasErrors(['avatarUpload' => 'image']);
});

it('rejects a profile photo larger than 2MB', function () {
    $user = User::factory()->create();

    $file = UploadedFile::fake()->image('big.jpg', 100, 100)->size(2049);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('avatarUpload', $file)
        ->assertHasErrors(['avatarUpload' => 'max']);
});

it('rejects an unsupported image format', function () {
    $user = User::factory()->create();

    $file = UploadedFile::fake()->image('avatar.gif', 400, 400);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('avatarUpload', $file)
        ->assertHasErrors(['avatarUpload' => 'mimes']);
});
