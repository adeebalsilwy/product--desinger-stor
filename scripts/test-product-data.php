<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Product Data for Designer Navigation\n";
echo "==============================================\n\n";

// Test specific product
$slug = 'ahlams-premium-evening-gown';
echo "Testing product: $slug\n";

$product = \App\Models\Product::where('slug', $slug)
    ->with(['images', 'productType'])
    ->first();

if ($product) {
    echo "\n✓ Product Found:\n";
    echo "  - ID: {$product->id}\n";
    echo "  - Name: {$product->name}\n";
    echo "  - Slug: {$product->slug}\n";
    echo "  - Price: {$product->price}\n";
    echo "  - Active: " . ($product->is_active ? 'YES' : 'NO') . "\n";
    
    echo "\n  Product Type:\n";
    if ($product->productType) {
        echo "    - ID: {$product->productType->id}\n";
        echo "    - Slug: {$product->productType->slug}\n";
        echo "    - Name: {$product->productType->name}\n";
    } else {
        echo "    - ⚠️ NOT LOADED (product_type_id: {$product->product_type_id})\n";
        
        // Try to load it manually
        $productType = \App\Models\ProductType::find($product->product_type_id);
        if ($productType) {
            echo "    - Manually loaded: {$productType->slug}\n";
        } else {
            echo "    - ✗ Product type not found in database!\n";
        }
    }
    
    echo "\n  Images:\n";
    if ($product->images && $product->images->count() > 0) {
        foreach ($product->images as $image) {
            echo "    - {$image->url}\n";
        }
    } else {
        echo "    - No images\n";
    }
    
    echo "\n✅ Designer Route Should Be:\n";
    $productTypeSlug = $product->productType ? $product->productType->slug : 't-shirt';
    echo "  /designer/{$productTypeSlug}/{$product->slug}\n";
    echo "\n";
} else {
    echo "✗ Product not found!\n";
}

// Check all products with missing product types
echo "\n\nChecking ALL products for proper product type loading:\n";
echo "=====================================================\n";

$allProducts = \App\Models\Product::with('productType')->get();
$issues = [];

foreach ($allProducts as $p) {
    if (!$p->productType) {
        $issues[] = [
            'slug' => $p->slug,
            'name' => $p->name,
            'product_type_id' => $p->product_type_id
        ];
    }
}

if (count($issues) > 0) {
    echo "\n⚠️ Products with MISSING product types:\n";
    foreach ($issues as $issue) {
        echo "  - {$issue['name']} ({$issue['slug']}) - type_id: {$issue['product_type_id']}\n";
    }
} else {
    echo "\n✅ All products have their product types loaded correctly!\n";
}
