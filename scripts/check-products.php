<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Product Types:\n";
$types = \App\Models\ProductType::all();
foreach($types as $type) {
    echo "- {$type->slug} (ID: {$type->id})\n";
}

echo "\nProducts:\n";
$products = \App\Models\Product::with('productType')->get();
foreach($products as $product) {
    $typeSlug = $product->productType ? $product->productType->slug : 'NO TYPE';
    echo "- {$product->slug} (Type: {$typeSlug})\n";
}
