<?php

namespace Database\Seeders;

use App\Models\ProductType;
use App\Models\Product;
use App\Models\PrintArea;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // T-Shirts
        $tshirt = ProductType::firstOrCreate(
            ['slug' => 't-shirt'],
            [
                'name' => 'T-Shirt',
                'description' => 'Customizable premium cotton t-shirts',
                'base_price' => 19.99,
                'is_active' => true,
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $tshirt->id, 'name' => 'front'],
            [
                'display_name' => 'Front Design',
                'width_mm' => 300,
                'height_mm' => 400,
                'offset_x' => 50,
                'offset_y' => 50,
                'is_default' => true,
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $tshirt->id, 'name' => 'back'],
            [
                'display_name' => 'Back Design',
                'width_mm' => 300,
                'height_mm' => 400,
                'offset_x' => 50,
                'offset_y' => 50,
                'is_default' => false,
            ]
        );

        // Mugs
        $mug = ProductType::firstOrCreate(
            ['slug' => 'coffee-mug'],
            [
                'name' => 'Coffee Mug',
                'description' => 'Ceramic coffee mugs with custom designs',
                'base_price' => 12.99,
                'is_active' => true,
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $mug->id, 'name' => 'wrap'],
            [
                'display_name' => 'Wrap Around',
                'width_mm' => 200,
                'height_mm' => 90,
                'offset_x' => 0,
                'offset_y' => 0,
                'is_default' => true,
            ]
        );

        // Phone Cases
        $phoneCase = ProductType::firstOrCreate(
            ['slug' => 'phone-case'],
            [
                'name' => 'Phone Case',
                'description' => 'Protective phone cases with custom prints',
                'base_price' => 24.99,
                'is_active' => true,
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $phoneCase->id, 'name' => 'back'],
            [
                'display_name' => 'Back Cover',
                'width_mm' => 75,
                'height_mm' => 150,
                'offset_x' => 0,
                'offset_y' => 0,
                'is_default' => true,
            ]
        );

        // Posters
        $poster = ProductType::firstOrCreate(
            ['slug' => 'poster'],
            [
                'name' => 'Poster',
                'description' => 'High-quality printed posters',
                'base_price' => 29.99,
                'is_active' => true,
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $poster->id, 'name' => 'full'],
            [
                'display_name' => 'Full Print',
                'width_mm' => 610,
                'height_mm' => 915,
                'offset_x' => 0,
                'offset_y' => 0,
                'is_default' => true,
            ]
        );

        // Tote Bags
        $tote = ProductType::firstOrCreate(
            ['slug' => 'tote-bag'],
            [
                'name' => 'Tote Bag',
                'description' => 'Eco-friendly canvas tote bags',
                'base_price' => 15.99,
                'is_active' => true,
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $tote->id, 'name' => 'front'],
            [
                'display_name' => 'Front Panel',
                'width_mm' => 250,
                'height_mm' => 300,
                'offset_x' => 25,
                'offset_y' => 50,
                'is_default' => true,
            ]
        );

        // Create sample products for each type
        $this->createSampleProducts($tshirt);
        $this->createSampleProducts($mug);
        $this->createSampleProducts($phoneCase);
        $this->createSampleProducts($poster);
        $this->createSampleProducts($tote);
    }
    
    protected function createSampleProducts(ProductType $productType)
    {
        Product::firstOrCreate(
            ['slug' => "standard-{$productType->slug}"],
            [
                'product_type_id' => $productType->id,
                'name' => "Standard {$productType->name}",
                'description' => "Our standard quality {$productType->name}",
                'sku' => strtoupper(str_replace('-', '', substr($productType->slug, 0, 3))) . '-STD-001',
                'price' => $productType->base_price,
                'inventory_count' => 100,
                'is_active' => true,
            ]
        );
        
        Product::firstOrCreate(
            ['slug' => "premium-{$productType->slug}"],
            [
                'product_type_id' => $productType->id,
                'name' => "Premium {$productType->name}",
                'description' => "Premium quality {$productType->name} with enhanced features",
                'sku' => strtoupper(str_replace('-', '', substr($productType->slug, 0, 3))) . '-PRM-001',
                'price' => $productType->base_price * 1.5,
                'inventory_count' => 50,
                'is_active' => true,
            ]
        );
    }
}
