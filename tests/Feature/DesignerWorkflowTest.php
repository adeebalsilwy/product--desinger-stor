<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SavedDesign;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\UserAsset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DesignerWorkflowTest extends TestCase
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
    public function it_follows_complete_designer_workflow()
    {
        // Step 1: Create a product type
        $productType = ProductType::factory()->create([
            'name' => 'T-Shirt',
            'slug' => 't-shirt',
        ]);

        // Step 2: Create a product
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'name' => 'Classic T-Shirt',
            'slug' => 'classic-t-shirt',
            'price' => 29.99,
        ]);

        // Step 3: Upload an image asset
        Storage::fake('public');
        $file = UploadedFile::fake()->image('logo.png', 400, 400);

        $assetResponse = $this->postJson('/api/assets/upload', [
            'file' => $file,
        ]);

        $assetResponse->assertStatus(201);
        $assetId = $assetResponse->json('data.id');

        // Step 4: Create a design using the asset
        $designResponse = $this->postJson('/api/designs', [
            'product_type_id' => $productType->id,
            'name' => 'My Custom Design',
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
                    ],
                    [
                        'type' => 'image',
                        'left' => 200,
                        'top' => 200,
                        'src' => $this->getFileUrl($assetId), // This would be the asset URL
                        'width' => 200,
                        'height' => 200,
                    ]
                ],
                'width' => 800,
                'height' => 800,
            ],
        ]);

        $designResponse->assertStatus(201);
        $designId = $designResponse->json('data.id');

        // Step 5: Verify design was created
        $this->assertDatabaseHas('saved_designs', [
            'id' => $designId,
            'user_id' => $this->user->id,
            'product_type_id' => $productType->id,
            'name' => 'My Custom Design',
        ]);

        // Step 6: Add design to cart
        $cartResponse = $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $designId,
            'quantity' => 1,
        ]);

        $cartResponse->assertStatus(200);

        // Step 7: Checkout
        $checkoutResponse = $this->postJson('/cart/checkout', [
            'shipping_address' => [
                'name' => 'John Doe',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'zip' => '12345',
                'country' => 'US',
            ],
        ]);

        $checkoutResponse->assertStatus(200);

        // Step 8: Verify order was created with design
        $order = \App\Models\Order::where('customer_id', $this->user->id)->latest()->first();
        $this->assertNotNull($order);

        $orderItem = $order->items()->where('saved_design_id', $designId)->first();
        $this->assertNotNull($orderItem);
        $this->assertEquals($designId, $orderItem->saved_design_id);
    }

    /** @test */
    public function it_can_edit_existing_design()
    {
        $productType = ProductType::factory()->create();
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
            'product_type_id' => $productType->id,
            'name' => 'Original Design',
            'design_data' => [
                'objects' => [
                    [
                        'type' => 'i-text',
                        'text' => 'Original Text',
                        'fill' => '#000000',
                    ]
                ]
            ]
        ]);

        // Update the design
        $response = $this->putJson("/api/designs/{$design->id}", [
            'name' => 'Updated Design',
            'design_data' => [
                'objects' => [
                    [
                        'type' => 'i-text',
                        'text' => 'Updated Text',
                        'fill' => '#ff0000',
                    ]
                ]
            ],
        ]);

        $response->assertStatus(200);

        // Verify update
        $this->assertDatabaseHas('saved_designs', [
            'id' => $design->id,
            'name' => 'Updated Design',
        ]);

        $updatedDesign = SavedDesign::find($design->id);
        $this->assertEquals('Updated Design', $updatedDesign->name);
        $this->assertEquals('Updated Text', $updatedDesign->design_data['objects'][0]['text']);
    }

    /** @test */
    public function it_can_duplicate_design_and_use_copy()
    {
        $productType = ProductType::factory()->create();
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
            'product_type_id' => $productType->id,
            'name' => 'Original Design',
        ]);

        // Duplicate the design
        $duplicateResponse = $this->postJson("/api/designs/{$design->id}/duplicate");
        $duplicateResponse->assertStatus(201);

        $copiedDesignId = $duplicateResponse->json('data.id');

        // Verify the copy was created
        $this->assertDatabaseHas('saved_designs', [
            'id' => $copiedDesignId,
            'name' => 'Original Design (Copy)',
            'user_id' => $this->user->id,
        ]);

        // Use the copied design in an order
        $product = Product::factory()->create(['product_type_id' => $productType->id]);

        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $copiedDesignId,
            'quantity' => 1,
        ]);

        $this->postJson('/cart/checkout', [
            'shipping_address' => [
                'name' => 'John Doe',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'zip' => '12345',
                'country' => 'US',
            ],
        ]);

        $order = \App\Models\Order::where('customer_id', $this->user->id)->latest()->first();
        $orderItem = $order->items()->where('saved_design_id', $copiedDesignId)->first();

        $this->assertNotNull($orderItem);
        $this->assertEquals($copiedDesignId, $orderItem->saved_design_id);
    }

    /** @test */
    public function it_can_save_design_as_template_and_use_it()
    {
        $productType = ProductType::factory()->create();
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
            'product_type_id' => $productType->id,
            'name' => 'Template Design',
        ]);

        // Save as template
        $response = $this->postJson("/api/designs/{$design->id}/save-as-template", [
            'name' => 'My Template',
            'category' => 't-shirts',
            'is_premium' => false,
        ]);

        $response->assertStatus(201);

        // Verify template was created
        $this->assertDatabaseHas('design_templates', [
            'name' => 'My Template',
            'product_type_id' => $productType->id,
            'created_by' => $this->user->id,
        ]);

        // Use the template to create a new design
        $template = \App\Models\DesignTemplate::where('name', 'My Template')->first();

        $useTemplateResponse = $this->postJson("/api/templates/{$template->id}/use");
        $useTemplateResponse->assertStatus(201);

        $newDesignId = $useTemplateResponse->json('data.id');

        // Verify new design was created from template
        $this->assertDatabaseHas('saved_designs', [
            'id' => $newDesignId,
            'user_id' => $this->user->id,
            'product_type_id' => $productType->id,
            'name' => 'My Template (Customized)',
        ]);

        // Add new design to cart
        $product = Product::factory()->create(['product_type_id' => $productType->id]);

        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $newDesignId,
            'quantity' => 1,
        ]);

        $this->postJson('/cart/checkout', [
            'shipping_address' => [
                'name' => 'John Doe',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'zip' => '12345',
                'country' => 'US',
            ],
        ]);

        $order = \App\Models\Order::where('customer_id', $this->user->id)->latest()->first();
        $orderItem = $order->items()->where('saved_design_id', $newDesignId)->first();

        $this->assertNotNull($orderItem);
    }

    /** @test */
    public function it_can_export_design_and_verify_file()
    {
        Storage::fake('public');

        $productType = ProductType::factory()->create();
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
            'product_type_id' => $productType->id,
        ]);

        // Export the design
        $response = $this->postJson("/api/designs/{$design->id}/export", [
            'format' => 'high_res',
        ]);

        $response->assertStatus(200);

        $exportUrl = $response->json('data.url');
        $this->assertNotNull($exportUrl);
        $this->assertStringContainsString('design_' . $design->id . '_highres', $exportUrl);

        // Verify file exists in storage
        $path = str_replace('/storage/', 'public/', parse_url($exportUrl, PHP_URL_PATH));
        Storage::assertExists($path);
    }

    /** @test */
    public function it_maintains_design_integrity_throughout_workflow()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'price' => 29.99,
        ]);

        // Create initial design with complex data
        $initialDesignData = [
            'version' => '5.3.0',
            'objects' => [
                [
                    'type' => 'i-text',
                    'left' => 100,
                    'top' => 100,
                    'text' => 'Custom Text',
                    'fontFamily' => 'Arial',
                    'fontSize' => 40,
                    'fill' => '#ff0000',
                ],
                [
                    'type' => 'rect',
                    'left' => 200,
                    'top' => 200,
                    'width' => 100,
                    'height' => 100,
                    'fill' => '#00ff00',
                ]
            ],
            'width' => 800,
            'height' => 800,
            'background' => '#ffffff',
        ];

        $designResponse = $this->postJson('/api/designs', [
            'product_type_id' => $productType->id,
            'name' => 'Complex Design',
            'design_data' => $initialDesignData,
        ]);

        $designResponse->assertStatus(201);
        $designId = $designResponse->json('data.id');

        // Retrieve the design and verify data integrity
        $getDesignResponse = $this->getJson("/api/designs/{$designId}");
        $getDesignResponse->assertStatus(200);

        $returnedDesign = $getDesignResponse->json('data');
        $this->assertEquals($initialDesignData, $returnedDesign['design_data']);

        // Add to cart and proceed to checkout
        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $designId,
            'quantity' => 1,
        ]);

        $this->postJson('/cart/checkout', [
            'shipping_address' => [
                'name' => 'John Doe',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'zip' => '12345',
                'country' => 'US',
            ],
        ]);

        // Verify order preserves design data
        $order = \App\Models\Order::where('customer_id', $this->user->id)->latest()->first();
        $orderItem = $order->items()->first();

        $this->assertEquals($designId, $orderItem->saved_design_id);
        $this->assertNotNull($orderItem->savedDesign);
        $this->assertEquals($initialDesignData, $orderItem->savedDesign->design_data);
    }

    private function getFileUrl($assetId)
    {
        // This is a helper to get the asset URL - in real implementation this would be the actual URL
        return "http://localhost/storage/user_assets/{$this->user->id}/test/logo.png";
    }
}
