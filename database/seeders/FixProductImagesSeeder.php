<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ShirtImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FixProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds to fix product images and thumbnail URLs.
     */
    public function run(): void
    {
        $this->command->info("🔧 Starting to fix product images and thumbnail URLs...");
        
        // Get all products
        $products = Product::all();
        
        $this->command->info("Processing {$products->count()} products...");
        
        foreach ($products as $product) {
            $this->command->info("\n📦 Processing: {$product->name} ({$product->slug})");
            
            // Check if product has images in the database
            $images = ShirtImage::where('tshirt_id', $product->id)
                ->orderBy('order')
                ->get();
            
            if ($images->count() > 0) {
                $this->command->info("  ✓ Found {$images->count()} image(s) in database");
                
                // Set first image as thumbnail if not already set
                if (!$product->thumbnail_url) {
                    $firstImage = $images->first();
                    $product->update(['thumbnail_url' => $firstImage->url]);
                    $this->command->info("  ✓ Set thumbnail URL: {$firstImage->url}");
                }
            } else {
                $this->command->warn("  ⚠️ No images found in database for this product");
                
                // If product has thumbnail_url but no database records, create them
                if ($product->thumbnail_url) {
                    $this->command->info("  ℹ️ Product has thumbnail_url but no ShirtImage records");
                    
                    // Create a ShirtImage record from thumbnail_url
                    ShirtImage::create([
                        'tshirt_id' => $product->id,
                        'url' => $product->thumbnail_url,
                        'order' => 1
                    ]);
                    $this->command->info("  ✓ Created ShirtImage record from thumbnail_url");
                }
            }
            
            // Verify image files exist in storage
            if ($product->thumbnail_url) {
                $imagePath = str_replace('/storage/', '', $product->thumbnail_url);
                if (!Storage::disk('public')->exists($imagePath)) {
                    $this->command->error("  ✗ Image file not found in storage: {$imagePath}");
                } else {
                    $this->command->info("  ✓ Image file exists in storage");
                }
            }
        }
        
        $this->command->info("\n✅ Product images fix completed!");
        $this->command->info("💡 Summary:");
        $this->command->info("  - Products with images: " . Product::whereHas('images')->count());
        $this->command->info("  - Products with thumbnail_url: " . Product::whereNotNull('thumbnail_url')->count());
        $this->command->info("  - Total ShirtImage records: " . ShirtImage::count());
    }
}
