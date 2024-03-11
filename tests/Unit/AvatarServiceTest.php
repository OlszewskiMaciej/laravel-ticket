<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AvatarServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $avatarService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->avatarService = new AvatarService();
        Storage::fake('public');
    }

    public function test_update_avatar()
    {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('avatar.jpg');

        $this->avatarService->updateAvatar($user, $file);

        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_update_avatar_replaces_existing_avatar()
    {
        $user = User::factory()->create(['avatar' => 'avatars/existing_avatar.jpg']);

        $file = UploadedFile::fake()->create('new_avatar.jpg');

        $this->avatarService->updateAvatar($user, $file);

        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertMissing('avatars/existing_avatar.jpg');
        Storage::disk('public')->assertExists($user->avatar);
    }
}
