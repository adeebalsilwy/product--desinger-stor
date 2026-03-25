<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==============================================\n";
echo "Product Type Selection Feature - Test Script\n";
echo "==============================================\n\n";

// Test 1: Check if route exists
echo "✓ Testing Route Registration...\n";
try {
    $route = route('designer.select-product-type', [], false);
    echo "  ✓ Route 'designer.select-product-type' exists: {$route}\n";
} catch (Exception $e) {
    echo "  ✗ Route 'designer.select-product-type' not found!\n";
}

try {
    $route = route('designer.my-designs', [], false);
    echo "  ✓ Route 'designer.my-designs' exists: {$route}\n";
} catch (Exception $e) {
    echo "  ✗ Route 'designer.my-designs' not found!\n";
}

try {
    $route = route('designer.create', ['productType' => 't-shirt'], false);
    echo "  ✓ Route 'designer.create' exists: {$route}\n";
} catch (Exception $e) {
    echo "  ✗ Route 'designer.create' not found!\n";
}

// Test 2: Check if ProductType model has image_url column
echo "\n✓ Testing ProductType Model...\n";
try {
    $productType = new \App\Models\ProductType();
    $fillable = $productType->getFillable();
    
    if (in_array('image_url', $fillable)) {
        echo "  ✓ 'image_url' is in ProductType fillable array\n";
    } else {
        echo "  ✗ 'image_url' NOT in ProductType fillable array\n";
    }
} catch (Exception $e) {
    echo "  ✗ Error checking ProductType: {$e->getMessage()}\n";
}

// Test 3: Check database column exists
echo "\n✓ Testing Database Schema...\n";
try {
    $hasColumn = \Schema::hasColumn('product_types', 'image_url');
    if ($hasColumn) {
        echo "  ✓ Column 'image_url' exists in 'product_types' table\n";
    } else {
        echo "  ✗ Column 'image_url' NOT found in 'product_types' table\n";
    }
} catch (Exception $e) {
    echo "  ✗ Error checking schema: {$e->getMessage()}\n";
}

// Test 4: Check if product types exist
echo "\n✓ Testing Product Types Data...\n";
try {
    $productTypes = \App\Models\ProductType::active()->get();
    
    if ($productTypes->count() > 0) {
        echo "  ✓ Found {$productTypes->count()} active product type(s):\n";
        
        foreach ($productTypes as $pt) {
            $hasImage = $pt->image_url ? 'YES' : 'NO';
            echo "    - {$pt->name} ({$pt->slug}) - Has Image: {$hasImage}\n";
        }
    } else {
        echo "  ⚠ No active product types found. Run seeder?\n";
    }
} catch (Exception $e) {
    echo "  ✗ Error fetching product types: {$e->getMessage()}\n";
}

// Test 5: Check API endpoint
echo "\n✓ Testing API Endpoint...\n";
try {
    // Simulate API call
    $controller = new \App\Http\Controllers\Api\ProductTypeController();
    $request = \Illuminate\Http\Request::create('/api/v1/product-types', 'GET');
    $response = $controller->index($request);
    
    $data = json_decode($response->getContent(), true);
    
    if (isset($data['data']) && count($data['data']) > 0) {
        echo "  ✓ API returns data successfully\n";
        echo "  ✓ Product types returned: " . count($data['data']) . "\n";
        
        // Check if image_url is present
        $first = $data['data'][0];
        if (isset($first['image_url'])) {
            echo "  ✓ 'image_url' field present in API response\n";
        } else {
            echo "  ⚠ 'image_url' field NOT in API response (will use fallback)\n";
        }
    } else {
        echo "  ⚠ API returned no data\n";
    }
} catch (Exception $e) {
    echo "  ✗ Error testing API: {$e->getMessage()}\n";
}

// Test 6: Check controller method exists
echo "\n✓ Testing DesignerController...\n";
try {
    $controller = new \App\Http\Controllers\Designer\DesignerController();
    
    if (method_exists($controller, 'selectProductType')) {
        echo "  ✓ Method 'selectProductType' exists in DesignerController\n";
    } else {
        echo "  ✗ Method 'selectProductType' NOT found in DesignerController\n";
    }
    
    if (method_exists($controller, 'myDesigns')) {
        echo "  ✓ Method 'myDesigns' exists in DesignerController\n";
    }
    
    if (method_exists($controller, 'create')) {
        echo "  ✓ Method 'create' exists in DesignerController\n";
    }
} catch (Exception $e) {
    echo "  ✗ Error checking DesignerController: {$e->getMessage()}\n";
}

// Test 7: Check Vue component files
echo "\n✓ Checking Vue Components...\n";
$selectComponent = __DIR__ . '/resources/js/Pages/Customer/Designer/SelectProductType.vue';
$myDesignsComponent = __DIR__ . '/resources/js/Pages/Customer/Designer/MyDesigns.vue';

if (file_exists($selectComponent)) {
    echo "  ✓ SelectProductType.vue exists\n";
    $content = file_get_contents($selectComponent);
    if (strpos($content, 'route(\'designer.select-product-type\')') !== false || 
        strpos($content, 'designer.select-product-type') !== false) {
        echo "    ✓ Component references correct route\n";
    }
} else {
    echo "  ✗ SelectProductType.vue NOT found\n";
}

if (file_exists($myDesignsComponent)) {
    echo "  ✓ MyDesigns.vue exists\n";
    $content = file_get_contents($myDesignsComponent);
    if (strpos($content, 'designer.select-product-type') !== false) {
        echo "    ✓ MyDesigns updated to use select-product-type route\n";
    } else {
        echo "    ⚠ MyDesigns may still use old route\n";
    }
} else {
    echo "  ✗ MyDesigns.vue NOT found\n";
}

// Test 8: Check translations
echo "\n✓ Checking Translations...\n";
$enTranslations = __DIR__ . '/resources/lang/en/customer.php';
$arTranslations = __DIR__ . '/resources/lang/ar/customer.php';

if (file_exists($enTranslations)) {
    $en = include $enTranslations;
    
    // Check nested array structure
    $designerKeys = [
        'select_product_title',
        'select_product_subtitle',
        'starting_from',
        'select',
        'back_to_designs',
    ];
    
    $found = 0;
    foreach ($designerKeys as $key) {
        if (isset($en['designer'][$key])) {
            $found++;
        }
    }
    
    if ($found == count($designerKeys)) {
        echo "  ✓ English translations present ({$found}/" . count($designerKeys) . ")\n";
    } else {
        echo "  ⚠ Some English translations missing ({$found}/" . count($designerKeys) . ")\n";
    }
}

if (file_exists($arTranslations)) {
    $ar = include $arTranslations;
    
    // Check nested array structure
    $designerKeys = [
        'select_product_title',
        'select_product_subtitle',
        'starting_from',
        'select',
        'back_to_designs',
    ];
    
    $found = 0;
    foreach ($designerKeys as $key) {
        if (isset($ar['designer'][$key])) {
            $found++;
        }
    }
    
    if ($found == count($designerKeys)) {
        echo "  ✓ Arabic translations present ({$found}/" . count($designerKeys) . ")\n";
    } else {
        echo "  ⚠ Some Arabic translations missing ({$found}/" . count($designerKeys) . ")\n";
    }
}

echo "\n==============================================\n";
echo "Test Summary\n";
echo "==============================================\n";
echo "If all tests show ✓, the feature is ready!\n";
echo "Open: http://127.0.0.1:8000/designer/my-designs\n";
echo "Then click 'Create New Design'\n";
echo "==============================================\n\n";
