<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ShirtImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class VerifyProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds to verify product images are correctly configured.
     */
    public function run(): void
    {
        $this->command->info("🔍 Verifying Product Images Configuration...\n");
        
        // Get all products
        $products = Product::all();
        
        $success = 0;
        $warning = 0;
        $error = 0;
        
        foreach ($products as $product) {
            $this->command->info("📦 Checking: {$product->name} ({$product->slug})");
            
            $hasIssues = false;
            
            // Check 1: Does product have thumbnail_url?
            if (!$product->thumbnail_url) {
                $this->command->warn("  ⚠️ Missing thumbnail_url");
                $hasIssues = true;
                $warning++;
            } else {
                $this->command->info("  ✓ Has thumbnail_url");
            }
            
            // Check 2: Does product have ShirtImage records?
            $images = ShirtImage::where('tshirt_id', $product->id)->count();
            if ($images === 0) {
                $this->command->warn("  ⚠️ No ShirtImage records in database");
                $hasIssues = true;
                $warning++;
            } else {
                $this->command->info("  ✓ Has {$images} ShirtImage record(s)");
            }
            
            // Check 3: If thumbnail_url exists, does the file exist in storage?
            if ($product->thumbnail_url) {
                $imagePath = str_replace('/storage/', '', $product->thumbnail_url);
                if (!Storage::disk('public')->exists($imagePath)) {
                    $this->command->error("  ✗ Image file not found in storage: {$imagePath}");
                    $hasIssues = true;
                    $error++;
                } else {
                    $this->command->info("  ✓ Image file exists in storage");
                }
            }
            
            // Check 4: Verify at least one ShirtImage record
            if ($images > 0) {
                $firstImage = ShirtImage::where('tshirt_id', $product->id)
                    ->orderBy('order')
                    ->first();
                    
                if ($firstImage) {
                    $imagePath = str_replace('/storage/', '', $firstImage->url);
                    if (!Storage::disk('public')->exists($imagePath)) {
                        $this->command->error("  ✗ ShirtImage file not found: {$imagePath}");
                        $hasIssues = true;
                        $error++;
                    } else {
                        $this->command->info("  ✓ First image file exists");
                    }
                }
            }
            
            if (!$hasIssues) {
                $this->command->info("  ✅ All checks passed\n");
                $success++;
            } else {
                $this->command->warn("  ⚠️ Issues detected - consider running FixProductImagesSeeder\n");
            }
        }
        
        $this->command->info("\n" . str_repeat("=", 50));
        $this->command->info("📊 Verification Summary:");
        $this->command->info(str_repeat("=", 50));
        $this->command->info("✅ Products OK:      {$success}");
        $this->command->info("⚠️  Products Warning: {$warning}");
        $this->command->info("❌ Products Error:   {$error}");
        $this->command->info("📦 Total Products:   {$products->count()}");
        $this->command->info(str_repeat("=", 50));
        
        if ($error > 0 || $warning > 0) {
            $this->command->warn("\n💡 ACTION REQUIRED:");
            $this->command->info("Run the following command to fix these issues:");
            $this->command->info("php artisan db:seed --class=FixProductImagesSeeder\n");
        } else {
            $this->command->info("\n✅ All products are properly configured!");
        }
    }
}
