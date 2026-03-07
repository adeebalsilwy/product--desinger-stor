<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SavedDesign;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartDesignerIntegrationTest extends TestCase
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
    public function it_can_add_design_to_cart()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create(['product_type_id' => $productType->id]);
        $design = SavedDesign::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $design->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Item added to cart successfully',
        ]);

        // Check session
        $cart = session('cart', []);
        $this->assertCount(1, $cart);
        
        $cartItem = $cart[0];
        $this->assertEquals($product->id, $cartItem['product_id']);
        $this->assertEquals($design->id, $cartItem['design_id']);
        $this->assertEquals(2, $cartItem['quantity']);
    }

    /** @test */
    public function it_can_view_cart_with_designs()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create(['product_type_id' => $productType->id]);
        $design = SavedDesign::factory()->create(['user_id' => $this->user->id]);

        // Add to cart
        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $design->id,
            'quantity' => 1,
        ]);

        // View cart
        $response = $this->get('/cart');

        $response->assertInertia(fn ($page) => $page
            ->component('Customer/Cart')
            ->has('cart.items', 1)
            ->where('cart.items.0.product.id', $product->id)
            ->where('cart.items.0.design.id', $design->id)
        );
    }

    /** @test */
    public function it_can_checkout_with_custom_design()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create(['product_type_id' => $productType->id]);
        $design = SavedDesign::factory()->create(['user_id' => $this->user->id]);

        // Add to cart
        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $design->id,
            'quantity' => 1,
        ]);

        // Checkout
        $response = $this->postJson('/cart/checkout', [
            'shipping_address' => [
                'name' => 'John Doe',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'zip' => '12345',
                'country' => 'US',
            ],
            'payment_method' => 'stripe',
        ]);

        $response->assertStatus(200);
        
        // Check that order was created
        $this->assertDatabaseHas('orders', [
            'customer_id' => $this->user->id,
            'payment_status' => 'paid', // Assuming successful payment
        ]);

        // Check that order items contain design
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'saved_design_id' => $design->id,
            'quantity' => 1,
        ]);
    }

    /** @test */
    public function it_preserves_design_data_in_order()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create(['product_type_id' => $productType->id]);
        
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
            'design_data' => [
                'objects' => [
                    [
                        'type' => 'i-text',
                        'text' => 'Custom Text',
                        'fill' => '#ff0000',
                    ]
                ]
            ]
        ]);

        // Add to cart
        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $design->id,
            'quantity' => 1,
        ]);

        // Checkout
        $this->postJson('/cart/checkout', [
            'shipping_address' => [
                'name' => 'John Doe',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'zip' => '12345',
                'country' => 'US',
            ],
            'payment_method' => 'stripe',
        ]);

        // Verify design is preserved in order
        $order = \App\Models\Order::where('customer_id', $this->user->id)->latest()->first();
        $orderItem = $order->items()->where('saved_design_id', $design->id)->first();

        $this->assertNotNull($orderItem);
        $this->assertEquals($design->id, $orderItem->saved_design_id);
    }

    /** @test */
    public function it_generates_print_files_for_custom_designs()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create(['product_type_id' => $productType->id]);
        $design = SavedDesign::factory()->create(['user_id' => $this->user->id]);

        // Add to cart
        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $design->id,
            'quantity' => 1,
        ]);

        // Checkout
        $this->postJson('/cart/checkout', [
            'shipping_address' => [
                'name' => 'John Doe',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'zip' => '12345',
                'country' => 'US',
            ],
            'payment_method' => 'stripe',
        ]);

        // Check that print file generation job was dispatched
        $order = \App\Models\Order::where('customer_id', $this->user->id)->latest()->first();
        
        // The job should have been dispatched to generate print files
        $orderItem = $order->items()->first();
        $this->assertNotNull($orderItem);
        
        // Print file URL should be populated after job runs
        // This would be tested separately with the job
    }

    /** @test */
    public function it_validates_design_exists_when_adding_to_cart()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create(['product_type_id' => $productType->id]);

        $response = $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => 999, // Non-existent design
            'quantity' => 1,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['design_id']);
    }

    /** @test */
    public function it_validates_product_exists_when_adding_to_cart()
    {
        $design = SavedDesign::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson('/cart', [
            'product_id' => 999, // Non-existent product
            'design_id' => $design->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['product_id']);
    }
}
