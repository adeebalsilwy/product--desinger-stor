<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking 'ahlams-bridal-accessories' product:\n\n";

$product = \App\Models\Product::where('slug', 'ahlams-bridal-accessories')
    ->with(['images', 'productType'])
    ->first();

if ($product) {
    echo "Found: {$product->name}\n";
    echo "Slug: {$product->slug}\n";
    echo "Type: " . ($product->productType ? $product->productType->slug : 'NONE') . "\n";
    echo "Price: {$product->price}\n";
    echo "Active: " . ($product->is_active ? 'YES' : 'NO') . "\n";
    
    echo "\nImages:\n";
    if ($product->images && $product->images->count() > 0) {
        foreach ($product->images as $image) {
            echo "- {$image->url}\n";
        }
    } else {
        echo "No images found!\n";
    }
} else {
    echo "Product not found!\n";
}
