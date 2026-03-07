<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserAsset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_upload_an_image_asset()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test-image.jpg', 800, 600);

        $response = $this->postJson('/api/assets/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'original_filename',
                'file_url',
                'width',
                'height',
            ],
            'message'
        ]);

        $assetId = $response->json('data.id');
        
        $this->assertDatabaseHas('user_assets', [
            'id' => $assetId,
            'user_id' => $this->user->id,
            'original_filename' => 'test-image.jpg',
            'width' => 800,
            'height' => 600,
        ]);

        Storage::disk('public')->assertExists("user_assets/{$this->user->id}/*/test-image.jpg");
    }

    /** @test */
    public function it_can_list_user_assets()
    {
        UserAsset::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/assets');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'original_filename',
                    'file_url',
                ],
            ],
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
        ]);

        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_delete_an_asset()
    {
        Storage::fake('public');
        
        $asset = UserAsset::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/assets/{$asset->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Asset deleted successfully',
        ]);

        $this->assertDatabaseMissing('user_assets', [
            'id' => $asset->id,
        ]);
    }

    /** @test */
    public function it_validates_uploaded_file()
    {
        $response = $this->postJson('/api/assets/upload', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    /** @test */
    public function it_rejects_invalid_file_types()
    {
        $file = UploadedFile::fake()->create('document.txt', 100, 'text/plain');

        $response = $this->postJson('/api/assets/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'error' => 'Invalid file type. Allowed: image/jpeg, image/png, image/gif, image/webp, image/svg+xml',
        ]);
    }

    /** @test */
    public function it_rejects_too_large_files()
    {
        $file = UploadedFile::fake()->image('large-image.jpg', 100, 100)->size(15000); // 15MB

        $response = $this->postJson('/api/assets/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_cannot_access_other_users_assets()
    {
        $otherUser = User::factory()->create();
        $asset = UserAsset::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/assets/{$asset->id}");

        $response->assertStatus(403); // Forbidden
    }
}
