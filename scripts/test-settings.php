<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Settings Functionality Test ===\n\n";

// Test 1: Check if settings table exists and has data
echo "1. Checking settings table...\n";
try {
    $settings = \App\Models\Setting::first();
    if ($settings) {
        echo "✓ Settings table exists and has data\n";
        echo "  Site Name: " . $settings->site_name . "\n";
        echo "  Site Currency: " . $settings->site_currency . "\n";
        echo "  Tax Rate: " . $settings->tax_rate . "\n";
        echo "  Brand Primary Color: " . $settings->brand_primary_color . "\n";
        echo "  Brand Tagline: " . $settings->brand_tagline . "\n\n";
    } else {
        echo "✗ Settings table is empty\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking settings: " . $e->getMessage() . "\n\n";
}

// Test 2: Check if admin user exists
echo "2. Checking admin user...\n";
try {
    $admin = \App\Models\User::where('role', 'admin')->first();
    if ($admin) {
        echo "✓ Admin user exists\n";
        echo "  Email: " . $admin->email . "\n";
        echo "  Role: " . $admin->role . "\n\n";
    } else {
        echo "✗ No admin user found\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking admin user: " . $e->getMessage() . "\n\n";
}

// Test 3: Check routes
echo "3. Checking settings routes...\n";
try {
    $routes = app('router')->getRoutes();
    $settingsRoutes = [];
    foreach ($routes as $route) {
        if (strpos($route->uri, 'admin/settings') !== false) {
            $settingsRoutes[] = [
                'uri' => $route->uri,
                'methods' => $route->methods,
                'name' => $route->getName()
            ];
        }
    }
    
    if (count($settingsRoutes) > 0) {
        echo "✓ Settings routes found:\n";
        foreach ($settingsRoutes as $route) {
            echo "  " . implode('|', $route['methods']) . " /" . $route['uri'];
            if ($route['name']) {
                echo " (" . $route['name'] . ")";
            }
            echo "\n";
        }
        echo "\n";
    } else {
        echo "✗ No settings routes found\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking routes: " . $e->getMessage() . "\n\n";
}

// Test 4: Test settings model functionality
echo "4. Testing settings model...\n";
try {
    // Test getSettings method
    $settings = \App\Models\Setting::getSettings();
    echo "✓ getSettings() method works\n";
    
    // Test getValue method
    $siteName = \App\Models\Setting::getValue('site_name', 'Default');
    echo "✓ getValue() method works: " . $siteName . "\n";
    
    // Test fillable attributes
    $fillable = $settings->getFillable();
    echo "✓ Model has " . count($fillable) . " fillable attributes\n";
    echo "  Including brand colors: " . (in_array('brand_primary_color', $fillable) ? '✓' : '✗') . "\n";
    echo "  Including SEO settings: " . (in_array('meta_keywords', $fillable) ? '✓' : '✗') . "\n";
    echo "  Including social media: " . (in_array('facebook_url', $fillable) ? '✓' : '✗') . "\n\n";
    
} catch (Exception $e) {
    echo "✗ Error testing settings model: " . $e->getMessage() . "\n\n";
}

echo "=== Test Complete ===\n";
echo "Server URL: http://127.0.0.1:8000/admin/settings\n";
echo "Login as admin user to test the settings page\n";