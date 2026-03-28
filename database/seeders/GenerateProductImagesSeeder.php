<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ShirtImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GenerateProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds to generate actual product images from templates.
     */
    public function run(): void
    {
        $this->command->info('🎨 Starting Product Image Generation from Templates...');
        
        // Get all template images from the template folder
        $templatePath = storage_path('app/public/template');
        
        if (!file_exists($templatePath)) {
            $this->command->error("Template folder not found: {$templatePath}");
            return;
        }
        
        // Get all image files from template directory
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $templateFiles = [];
        
        foreach (scandir($templatePath) as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $validExtensions) && is_file($templatePath . '/' . $file)) {
                $templateFiles[] = $file;
            }
        }
        
        if (empty($templateFiles)) {
            $this->command->error("No template images found in: {$templatePath}");
            return;
        }
        
        $this->command->info("✅ Found " . count($templateFiles) . " template images");
        
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
            
            // Generate new images using random templates
            $this->generateProductImages($product, $productImagesFolder, $templateFiles, $templatePath);
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info("\n✅ Product image generation completed!");
    }
    
    /**
     * Generate images for a product using random templates
     */
    private function generateProductImages($product, $folder, $templateFiles, $templatePath)
    {
        // Shuffle template files to select randomly
        shuffle($templateFiles);
        
        // Select 5 random templates for this product
        $selectedTemplates = array_slice($templateFiles, 0, 5);
        
        // If we have less than 5 templates, use what we have
        if (count($selectedTemplates) < 5) {
            $selectedTemplates = array_merge($selectedTemplates, array_slice($templateFiles, 0, 5 - count($selectedTemplates)));
        }
        
        $imageTypes = ['main', 'thumbnail', 'additional_1', 'additional_2', 'additional_3'];
        $imageOrder = 1;
        
        foreach ($selectedTemplates as $index => $templateFile) {
            try {
                $imageType = $imageTypes[$index] ?? 'additional_' . $index;
                $imageName = "{$imageType}_{$product->slug}." . pathinfo($templateFile, PATHINFO_EXTENSION);
                $imagePath = "{$folder}/{$imageName}";
                
                // Copy template image to product folder
                $sourcePath = $templatePath . '/' . $templateFile;
                Storage::disk('public')->put($imagePath, file_get_contents($sourcePath));
                
                // Save to database
                ShirtImage::create([
                    'tshirt_id' => $product->id,
                    'url' => '/storage/' . $imagePath,
                    'order' => $imageOrder++
                ]);
                
                $this->command->info("  ✓ Generated: {$imageName} (from template: {$templateFile})");
                
            } catch (\Exception $e) {
                $this->command->error("  ✗ Failed to generate {$imageType}: " . $e->getMessage());
            }
        }
        
        // Set first image as thumbnail
        $firstImage = $product->images()->orderBy('order')->first();
        if ($firstImage && !$product->thumbnail_url) {
            $product->update(['thumbnail_url' => $firstImage->url]);
            $this->command->info("  ✓ Set thumbnail URL");
        }
    }
    

    

}
