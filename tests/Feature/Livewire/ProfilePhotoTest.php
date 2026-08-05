<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Profile\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('filesystems.default'));
    }

    public function test_user_can_upload_profile_photo(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $file = UploadedFile::fake()->image('avatar.jpg', 400, 400);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('avatarUpload', $file);

        $user->refresh();

        $this->assertNotNull($user->avatar);
        Storage::disk(config('filesystems.default'))->assertExists($user->avatar);
    }

    public function test_owner_can_upload_and_replace_profile_photo(): void
    {
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

        $this->assertNotNull($user->avatar);
        $this->assertNotEquals($oldPath, $user->avatar);
        Storage::disk(config('filesystems.default'))->assertMissing($oldPath);
        Storage::disk(config('filesystems.default'))->assertExists($user->avatar);
    }

    public function test_admin_can_delete_profile_photo(): void
    {
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

        $this->assertNull($user->avatar);
        Storage::disk(config('filesystems.default'))->assertMissing($fakePath);
    }

    public function test_rejects_invalid_file_type_or_large_size(): void
    {
        $user = User::factory()->create();

        $largeFile = UploadedFile::fake()->create('document.pdf', 3000);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('avatarUpload', $largeFile)
            ->assertHasErrors(['avatarUpload']);
    }
}
