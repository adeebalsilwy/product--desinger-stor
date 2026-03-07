<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Product Types Table
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_active');
        });

        // Products Table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('inventory_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('thumbnail_url')->nullable();
            $table->timestamps();
            
            $table->index(['product_type_id', 'is_active']);
            $table->index('slug');
        });

        // Print Areas Table
        Schema::create('print_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // front, back, sleeve, etc.
            $table->string('display_name')->nullable();
            $table->decimal('width_mm', 8, 2);
            $table->decimal('height_mm', 8, 2);
            $table->integer('offset_x')->default(0);
            $table->integer('offset_y')->default(0);
            $table->string('preview_image_url')->nullable();
            $table->string('template_image_url')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index('product_type_id');
        });

        // Product Variants Table
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('variant_type'); // size, color, material
            $table->string('variant_value'); // XL, red, cotton
            $table->decimal('price_modifier', 10, 2)->default(0.00);
            $table->integer('inventory_count')->default(0);
            $table->string('sku')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'variant_type']);
        });

        // Design Templates Table
        Schema::create('design_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('product_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('preview_url')->nullable();
            $table->json('design_data');
            $table->boolean('is_premium')->default(false);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('usage_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('product_type_id');
            $table->index('category');
            $table->index('is_premium');
        });

        // Saved Designs Table
        Schema::create('saved_designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->foreignId('product_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->json('design_data');
            $table->string('thumbnail_url')->nullable();
            $table->string('preview_url')->nullable();
            $table->json('print_files')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_template')->default(false);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('session_id');
            $table->index('product_type_id');
            $table->index('is_public');
        });

        // Fonts Table
        Schema::create('fonts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('display_name')->nullable();
            $table->string('font_file_url');
            $table->string('preview_url')->nullable();
            $table->string('license_type')->default('free');
            $table->boolean('is_active')->default(true);
            $table->string('category')->nullable();
            $table->timestamps();
            
            $table->index('is_active');
        });

        // Clipart Categories Table
        Schema::create('clipart_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('clipart_categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Cliparts Table
        Schema::create('cliparts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('clipart_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image_url');
            $table->string('thumbnail_url')->nullable();
            $table->longText('svg_content')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('download_count')->default(0);
            $table->timestamps();
            
            $table->index('clipart_category_id');
        });

        // User Assets Table
        Schema::create('user_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path');
            $table->string('file_url');
            $table->string('thumbnail_url')->nullable();
            $table->string('file_type')->default('image');
            $table->integer('file_size')->nullable();
            $table->string('mime_type');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('storage_disk')->default('public');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('session_id');
            $table->index('file_type');
        });

        // Update Orders Table
        Schema::table('orders', function (Blueprint $table) {
            $table->json('design_data')->nullable()->after('payment_status');
            $table->json('shipping_address')->nullable()->after('design_data');
            $table->json('billing_address')->nullable()->after('shipping_address');
            $table->text('notes')->nullable()->after('billing_address');
            $table->string('coupon_code')->nullable()->after('notes');
            $table->decimal('discount_amount', 10, 2)->default(0.00)->after('coupon_code');
            $table->decimal('shipping_cost', 10, 2)->default(0.00)->after('discount_amount');
            $table->decimal('tax_amount', 10, 2)->default(0.00)->after('shipping_cost');
        });

        // Order Items Table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('saved_design_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->json('customization_data')->nullable();
            $table->string('print_file_url')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('status');
        });

        // Order Histories Table
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at');
        });

        // Coupons Table
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed', 'free_shipping']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_order_amount', 10, 2)->default(0.00);
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->json('applicable_products')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });

        // Payment Transactions Table
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method');
            $table->string('transaction_id');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('status')->default('pending');
            $table->json('response_data')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('transaction_id');
        });

        // Settings Table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->text('setting_value')->nullable();
            $table->string('setting_type')->default('string');
            $table->string('group_name')->nullable();
            $table->string('display_name')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            $table->index('setting_key');
            $table->index('group_name');
        });

        // Design Analytics Table
        Schema::create('design_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->foreignId('product_type_id')->nullable()->constrained()->nullOnDelete();
            $table->json('design_data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');
            
            $table->index('event_type');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_analytics');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('order_histories');
        Schema::dropIfExists('order_items');
        
        // Remove order columns
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'design_data', 'shipping_address', 'billing_address', 
                'notes', 'coupon_code', 'discount_amount', 
                'shipping_cost', 'tax_amount'
            ]);
        });
        
        Schema::dropIfExists('user_assets');
        Schema::dropIfExists('cliparts');
        Schema::dropIfExists('clipart_categories');
        Schema::dropIfExists('fonts');
        Schema::dropIfExists('saved_designs');
        Schema::dropIfExists('design_templates');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('print_areas');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_types');
    }
};
