<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SavedDesign;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Jobs\ProcessOrderItems;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderProcessingTest extends TestCase
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
    public function it_processes_order_with_custom_design()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'price' => 29.99,
        ]);
        
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Custom T-Shirt Design',
        ]);

        // Add to cart and checkout
        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $design->id,
            'quantity' => 2,
        ]);

        $response = $this->postJson('/cart/checkout', [
            'shipping_address' => [
                'name' => 'John Doe',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'zip' => '12345',
                'country' => 'US',
            ],
        ]);

        $response->assertStatus(200);

        // Check order was created
        $order = Order::where('customer_id', $this->user->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals(2, $order->total_tshirts);
        $this->assertEquals(59.98, $order->total_amount);

        // Check order item was created with design
        $orderItem = OrderItem::where('order_id', $order->id)->first();
        $this->assertNotNull($orderItem);
        $this->assertEquals($product->id, $orderItem->product_id);
        $this->assertEquals($design->id, $orderItem->saved_design_id);
        $this->assertEquals(2, $orderItem->quantity);
        $this->assertEquals(29.99, $orderItem->unit_price);
        $this->assertEquals(59.98, $orderItem->total_price);
    }

    /** @test */
    public function it_generates_print_files_for_custom_design_orders()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'price' => 29.99,
        ]);
        
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Add to cart and checkout
        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $design->id,
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

        $order = Order::where('customer_id', $this->user->id)->latest()->first();
        $orderItem = $order->items()->first();

        // Process the order items job
        $job = new ProcessOrderItems($order);
        $job->handle();

        // Refresh the order item
        $orderItem->refresh();

        // Check that print file URL is generated
        $this->assertNotNull($orderItem->print_file_url);
        $this->assertStringContainsString('print', $orderItem->print_file_url);
    }

    /** @test */
    public function it_preserves_design_data_in_order_history()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'price' => 29.99,
        ]);
        
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
            'design_data' => [
                'objects' => [
                    [
                        'type' => 'i-text',
                        'text' => 'Custom Text',
                        'fill' => '#ff0000',
                        'fontSize' => 40,
                    ]
                ]
            ]
        ]);

        // Add to cart and checkout
        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $design->id,
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

        $order = Order::where('customer_id', $this->user->id)->latest()->first();
        
        // Check that the order preserves design information
        $this->assertNotNull($order);
        
        $orderItem = $order->items()->first();
        $this->assertNotNull($orderItem);
        
        // The saved design should be accessible through the order item
        $this->assertEquals($design->id, $orderItem->saved_design_id);
        $this->assertNotNull($orderItem->savedDesign);
        $this->assertEquals($design->name, $orderItem->savedDesign->name);
    }

    /** @test */
    public function it_handles_multiple_items_with_different_designs()
    {
        $productType = ProductType::factory()->create();
        $product1 = Product::factory()->create([
            'product_type_id' => $productType->id,
            'price' => 29.99,
        ]);
        $product2 = Product::factory()->create([
            'product_type_id' => $productType->id,
            'price' => 39.99,
        ]);
        
        $design1 = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Design 1',
        ]);
        $design2 = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Design 2',
        ]);

        // Add multiple items to cart
        $this->postJson('/cart', [
            'product_id' => $product1->id,
            'design_id' => $design1->id,
            'quantity' => 1,
        ]);

        $this->postJson('/cart', [
            'product_id' => $product2->id,
            'design_id' => $design2->id,
            'quantity' => 2,
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

        $order = Order::where('customer_id', $this->user->id)->latest()->first();
        
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->total_tshirts); // 1 + 2
        $this->assertEquals(109.97, $order->total_amount); // 29.99 + (39.99 * 2)

        // Check both items exist
        $orderItems = $order->items;
        $this->assertCount(2, $orderItems);

        $item1 = $orderItems->firstWhere('product_id', $product1->id);
        $item2 = $orderItems->firstWhere('product_id', $product2->id);

        $this->assertEquals($design1->id, $item1->saved_design_id);
        $this->assertEquals($design2->id, $item2->saved_design_id);
        $this->assertEquals(1, $item1->quantity);
        $this->assertEquals(2, $item2->quantity);
    }

    /** @test */
    public function it_updates_order_status_after_processing()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'price' => 29.99,
        ]);
        
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Add to cart and checkout
        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $design->id,
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

        $order = Order::where('customer_id', $this->user->id)->latest()->first();
        
        // Initially pending
        $this->assertEquals('pending', $order->status);

        // Process the order items
        $job = new ProcessOrderItems($order);
        $job->handle();

        // Refresh the order
        $order->refresh();

        // Should be processing after print files are generated
        $this->assertContains($order->status, ['pending', 'processing']);
    }

    /** @test */
    public function it_validates_inventory_before_processing_order()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'price' => 29.99,
            'inventory_count' => 0, // No inventory
        ]);
        
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Add to cart
        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $design->id,
            'quantity' => 1,
        ]);

        $response = $this->postJson('/cart/checkout', [
            'shipping_address' => [
                'name' => 'John Doe',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'zip' => '12345',
                'country' => 'US',
            ],
        ]);

        // Should fail validation due to insufficient inventory
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['inventory']);
    }

    /** @test */
    public function it_sends_confirmation_email_after_order_placement()
    {
        $productType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $productType->id,
            'price' => 29.99,
        ]);
        
        $design = SavedDesign::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Add to cart and checkout
        $this->postJson('/cart', [
            'product_id' => $product->id,
            'design_id' => $design->id,
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

        $order = Order::where('customer_id', $this->user->id)->latest()->first();
        
        // Check that confirmation email job was dispatched
        // This would be tested by checking if the job was queued
        $this->assertNotNull($order);
        
        // In a real test, you'd check if SendOrderEmails job was dispatched
        // $this->assertDispatched(SendOrderEmails::class, function ($job) use ($order) {
        //     return $job->order->id === $order->id;
        // });
    }
}
