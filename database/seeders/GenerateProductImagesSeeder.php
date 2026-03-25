<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ShirtImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Laravel\Facades\Image;

class GenerateProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds to generate actual product images from templates.
     */
    public function run(): void
    {
        $this->command->info('🎨 Starting Product Image Generation from Templates...');
        
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Get all products
        $products = \App\Models\Product::with('productType')->get();
        
        $this->command->info("Processing {$products->count()} products...");
        
        foreach ($products as $product) {
            $this->command->info("\n📦 Processing: {$product->name} ({$product->slug})");
            
            // Create a dedicated folder for this product's images
            $productImagesFolder = 'products/' . $product->slug;
            
            if (!Storage::disk('public')->exists($productImagesFolder)) {
                Storage::disk('public')->makeDirectory($productImagesFolder);
                $this->command->info("  ✓ Created folder: {$productImagesFolder}");
            }
            
            // Delete old images and records
            $oldImages = ShirtImage::where('tshirt_id', $product->id)->get();
            foreach ($oldImages as $oldImage) {
                $oldPath = str_replace('/storage/', '', $oldImage->url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                $oldImage->delete();
            }
            $this->command->info("  ✓ Cleaned old images");
            
            // Generate new images based on product type
            $this->generateProductImages($product, $productImagesFolder);
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info("\n✅ Product image generation completed!");
    }
    
    /**
     * Generate images for a product
     */
    private function generateProductImages($product, $folder)
    {
        // Define base colors based on product type
        $colors = $this->getColorsForProduct($product);
        
        // Generate 5 images with different backgrounds/text
        $imageConfigs = [
            ['type' => 'main', 'color' => $colors[0], 'text' => $product->name],
            ['type' => 'thumbnail', 'color' => $colors[1], 'text' => 'Thumbnail'],
            ['type' => 'additional_1', 'color' => $colors[2], 'text' => 'Detail View'],
            ['type' => 'additional_2', 'color' => $colors[3], 'text' => 'Side View'],
            ['type' => 'additional_3', 'color' => $colors[4], 'text' => 'Back View'],
        ];
        
        $imageOrder = 1;
        
        foreach ($imageConfigs as $config) {
            try {
                $imageName = "{$config['type']}_{$product->slug}.jpg";
                $imagePath = "{$folder}/{$imageName}";
                
                // Generate the image
                $this->createProductImage(
                    $imagePath,
                    $config['color'],
                    $config['text'],
                    $product->productType ? $product->productType->name : 'Product'
                );
                
                // Save to database
                ShirtImage::create([
                    'tshirt_id' => $product->id,
                    'url' => '/storage/' . $imagePath,
                    'order' => $imageOrder++
                ]);
                
                $this->command->info("  ✓ Generated: {$imageName}");
                
            } catch (\Exception $e) {
                $this->command->error("  ✗ Failed to generate {$config['type']}: " . $e->getMessage());
            }
        }
        
        // Set first image as thumbnail
        $firstImage = $product->images()->orderBy('order')->first();
        if ($firstImage && !$product->thumbnail_url) {
            $product->update(['thumbnail_url' => $firstImage->url]);
            $this->command->info("  ✓ Set thumbnail URL");
        }
    }
    
    /**
     * Create a single product image using Intervention Image v3
     */
    private function createProductImage($path, $backgroundColor, $text, $productType)
    {
        // Create simple elegant gradient background image
        $img = Image::create(1000, 1000, $backgroundColor);
        
        // Add subtle gradient overlay
        for ($y = 0; $y < 1000; $y += 20) {
            $brightness = 1 + ($y / 1000) * 0.3;
            $img->fill('rgba(255,255,255,0.05)', 0, $y, 1000, $y + 20);
        }
        
        // Add elegant centered circle design
        $centerColor = '#ffffff';
        $img->circle(500, 400, 150, function($draw) use ($centerColor) {
            $draw->background($centerColor);
            $draw->opacity(0.25);
        });
        
        $img->circle(500, 400, 100, function($draw) use ($centerColor) {
            $draw->border(3, $centerColor);
            $draw->opacity(0.4);
        });
        
        // Add text at bottom third
        try {
            $img->text($text, 500, 850, function($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(36);
                $font->color('#ffffff');
                $font->align('center');
            });
        } catch (\Exception $e) {
            $img->text($text, 500, 850, function($font) {
                $font->size(36);
                $font->color('#ffffff');
                $font->align('center');
            });
        }
        
        // Add brand watermark at top
        try {
            $img->text("Ahlam's Girls", 500, 50, function($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(28);
                $font->color('#ffd700');
                $font->align('center');
            });
        } catch (\Exception $e) {
            $img->text("Ahlam's Girls", 500, 50, function($font) {
                $font->size(28);
                $font->color('#ffd700');
                $font->align('center');
            });
        }
        
        // Save as high-quality JPEG
        Storage::disk('public')->put($path, $img->toJpeg(95));
    }
    
    /**
     * Get colors for product based on type
     */
    private function getColorsForProduct($product)
    {
        $productType = $product->productType ? strtolower($product->productType->name) : '';
        
        if (str_contains($productType, 'dress') || str_contains($productType, 'evening')) {
            return ['#1a1a2e', '#16213e', '#0f3460', '#e94560', '#533483'];
        } elseif (str_contains($productType, 'abaya')) {
            return ['#000000', '#1a1a1a', '#2d2d2d', '#3d3d3d', '#4a4a4a'];
        } elseif (str_contains($productType, 'casual') || str_contains($productType, 't-shirt')) {
            return ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7'];
        } elseif (str_contains($productType, 'wedding')) {
            return ['#f8e1e7', '#f0c1d1', '#e8a1b9', '#df81a1', '#d66189'];
        } else {
            return ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6'];
        }
    }
}
