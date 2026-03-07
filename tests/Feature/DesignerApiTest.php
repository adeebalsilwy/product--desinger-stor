<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SavedDesign;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\UserAsset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignerApiTest extends TestCase
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
    public function it_can_create_a_design()
    {
        $productType = ProductType::factory()->create();

        $response = $this->postJson('/api/designs', [
            'product_type_id' => $productType->id,
            'name' => 'Test Design',
            'design_data' => [
                'version' => '5.3.0',
                'objects' => [
                    [
                        'type' => 'i-text',
                        'left' => 100,
                        'top' => 100,
                        'text' => 'Hello World',
                        'fontFamily' => 'Arial',
                        'fontSize' => 40,
                        'fill' => '#000000',
                    ]
                ],
                'width' => 800,
                'height' => 800,
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'product_type_id',
                'design_data',
                'created_at',
                'updated_at',
            ],
            'message'
        ]);

        $this->assertDatabaseHas('saved_designs', [
            'name' => 'Test Design',
            'user_id' => $this->user->id,
            'product_type_id' => $productType->id,
        ]);
    }

    /** @test */
    public function it_can_update_a_design()
    {
        $design = SavedDesign::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/designs/{$design->id}", [
            'name' => 'Updated Design',
            'design_data' => [
                'version' => '5.3.0',
                'objects' => [
                    [
                        'type' => 'i-text',
                        'left' => 200,
                        'top' => 200,
                        'text' => 'Updated Text',
                        'fontFamily' => 'Times New Roman',
                        'fontSize' => 50,
                        'fill' => '#ff0000',
                    ]
                ],
                'width' => 800,
                'height' => 800,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $design->id,
                'name' => 'Updated Design',
            ],
            'message' => 'Design updated successfully',
        ]);

        $this->assertDatabaseHas('saved_designs', [
            'id' => $design->id,
            'name' => 'Updated Design',
        ]);
    }

    /** @test */
    public function it_can_delete_a_design()
    {
        $design = SavedDesign::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/designs/{$design->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Design deleted successfully',
        ]);

        $this->assertDatabaseMissing('saved_designs', [
            'id' => $design->id,
        ]);
    }

    /** @test */
    public function it_can_list_user_designs()
    {
        SavedDesign::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/designs');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'product_type_id',
                    'created_at',
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
    public function it_can_duplicate_a_design()
    {
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Design',
        ]);

        $response = $this->postJson("/api/designs/{$design->id}/duplicate");

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'name' => 'Original Design (Copy)',
                'user_id' => $this->user->id,
                'product_type_id' => $design->product_type_id,
            ],
            'message' => 'Design duplicated successfully',
        ]);

        $this->assertDatabaseHas('saved_designs', [
            'name' => 'Original Design (Copy)',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_requires_authentication_for_design_operations()
    {
        $productType = ProductType::factory()->create();

        $response = $this->postJson('/api/designs', [
            'product_type_id' => $productType->id,
            'name' => 'Test Design',
            'design_data' => [
                'version' => '5.3.0',
                'objects' => [],
                'width' => 800,
                'height' => 800,
            ],
        ]);

        $response->assertStatus(409); // Unauthenticated
    }

    /** @test */
    public function it_validates_design_creation_inputs()
    {
        $response = $this->postJson('/api/designs', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['product_type_id', 'design_data']);
    }

    /** @test */
    public function it_cannot_access_other_users_designs()
    {
        $otherUser = User::factory()->create();
        $design = SavedDesign::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/designs/{$design->id}");

        $response->assertStatus(403); // Forbidden
    }
}
