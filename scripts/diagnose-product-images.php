<?php

/**
 * Diagnostic Script for Product Images
 * 
 * Usage: php scripts/diagnose-product-images.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\ShirtImage;
use Illuminate\Support\Facades\Storage;

echo "🔍 Diagnosing Product Images...\n\n";

// Get all products
$products = Product::all();

$totalProducts = $products->count();
$productsWithImages = 0;
$productsWithThumbnail = 0;
$brokenImages = 0;

echo "📊 Overview:\n";
echo "  Total Products: {$totalProducts}\n\n";

foreach ($products as $product) {
    echo "📦 Product: {$product->name}\n";
    echo "   Slug: {$product->slug}\n";
    
    // Check database images
    $images = ShirtImage::where('tshirt_id', $product->id)
        ->orderBy('order')
        ->get();
    
    if ($images->count() > 0) {
        echo "   ✓ Database Images: {$images->count()} found\n";
        $productsWithImages++;
        
        // Show first image
        $firstImage = $images->first();
        echo "   → First Image URL: {$firstImage->url}\n";
        
        // Check if file exists
        $imagePath = str_replace('/storage/', '', $firstImage->url);
        if (Storage::disk('public')->exists($imagePath)) {
            echo "   ✓ File exists in storage\n";
        } else {
            echo "   ✗ File MISSING from storage: {$imagePath}\n";
            $brokenImages++;
        }
    } else {
        echo "   ⚠️ Database Images: NONE found\n";
    }
    
    // Check thumbnail_url
    if ($product->thumbnail_url) {
        echo "   ✓ Thumbnail URL: Set ({$product->thumbnail_url})\n";
        $productsWithThumbnail++;
        
        // Verify thumbnail file exists
        $thumbPath = str_replace('/storage/', '', $product->thumbnail_url);
        if (!Storage::disk('public')->exists($thumbPath)) {
            echo "   ✗ Thumbnail file MISSING from storage: {$thumbPath}\n";
            $brokenImages++;
        }
    } else {
        echo "   ✗ Thumbnail URL: NOT SET\n";
    }
    
    echo "\n";
}

echo "\n";
echo "📊 Summary:\n";
echo "  ──────────────────────────────\n";
echo "  Total Products:              {$totalProducts}\n";
echo "  Products with Images:        {$productsWithImages}\n";
echo "  Products with Thumbnail URL: {$productsWithThumbnail}\n";
echo "  Broken/Missing Images:       {$brokenImages}\n";
echo "\n";

if ($brokenImages > 0) {
    echo "⚠️  WARNING: {$brokenImages} broken/missing images detected!\n";
    echo "   Run: php artisan db:seed --class=FixProductImagesSeeder\n";
} else if ($productsWithThumbnail < $totalProducts) {
    echo "⚠️  WARNING: Some products missing thumbnail URLs!\n";
    echo "   Run: php artisan db:seed --class=FixProductImagesSeeder\n";
} else {
    echo "✅ All products have proper images configured!\n";
}

echo "\n";
echo "💡 Recommendation:\n";
echo "   If you see any issues above, run the fix seeder:\n";
echo "   php artisan db:seed --class=FixProductImagesSeeder\n";
echo "\n";
