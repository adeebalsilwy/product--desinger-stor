<?php

/**
 * Settings Update Diagnostic Tool
 * 
 * This script helps diagnose issues with settings updates
 * Run this from the browser or command line
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

echo "==============================================\n";
echo "SETTINGS UPDATE DIAGNOSTIC TOOL\n";
echo "==============================================\n\n";

// Test 1: Database Connection
echo "1. Testing Database Connection...\n";
try {
    DB::connection()->getPdo();
    echo "   ✓ Database connection successful\n";
    echo "   Database: " . DB::connection()->getDatabaseName() . "\n";
} catch (\Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// Test 2: Check Settings Table Structure
echo "2. Checking Settings Table Structure...\n";
try {
    $columns = DB::select('DESCRIBE settings');
    echo "   ✓ Settings table exists\n";
    echo "   Columns (" . count($columns) . "):\n";
    foreach ($columns as $column) {
        echo "     - {$column->Field} ({$column->Type})\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Failed to check table structure: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Check Existing Settings Record
echo "3. Checking Existing Settings Record...\n";
try {
    $settings = Setting::first();
    if ($settings) {
        echo "   ✓ Settings record found (ID: {$settings->id})\n";
        echo "   Current values:\n";
        echo "     - Site Name: " . ($settings->site_name ?? 'null') . "\n";
        echo "     - Brand Primary Color: " . ($settings->brand_primary_color ?? 'null') . "\n";
        echo "     - Brand Tagline: " . ($settings->brand_tagline ?? 'null') . "\n";
        echo "     - Site Email: " . ($settings->site_email ?? 'null') . "\n";
    } else {
        echo "   ⚠ No settings record found. Will create one.\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Failed to retrieve settings: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Test Settings Update
echo "4. Testing Settings Update...\n";
try {
    if (!$settings) {
        echo "   Creating new settings record...\n";
        $settings = Setting::create([
            'site_name' => 'Test Site',
            'brand_primary_color' => '#1a1a2e',
            'brand_secondary_color' => '#16213e',
            'brand_accent_color' => '#e94560',
            'brand_bg_color' => '#0f3460',
            'brand_text_color' => '#ffffff',
            'brand_tagline' => 'Test Tagline',
            'site_currency' => 'USD',
            'products_per_page' => 12,
            'enable_registration' => true,
            'enable_reviews' => true,
        ]);
        echo "   ✓ New settings record created\n";
    }
    
    // Update test
    echo "   Updating settings...\n";
    $originalName = $settings->site_name;
    $testName = 'Updated Test Site ' . time();
    
    $settings->update([
        'site_name' => $testName,
        'site_description' => 'Test description updated at ' . date('Y-m-d H:i:s'),
    ]);
    
    // Refresh from database
    $settings->refresh();
    
    if ($settings->site_name === $testName) {
        echo "   ✓ Settings update successful!\n";
        echo "   Old name: {$originalName}\n";
        echo "   New name: {$settings->site_name}\n";
        
        // Restore original name
        $settings->update(['site_name' => $originalName]);
        echo "   Restored original name: {$settings->site_name}\n";
    } else {
        echo "   ✗ Settings update failed! Value not changed in database.\n";
        echo "   Expected: {$testName}\n";
        echo "   Got: {$settings->site_name}\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Settings update failed: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}
echo "\n";

// Test 5: Check Storage Permissions
echo "5. Checking Storage Permissions...\n";
try {
    $disk = Storage::disk('public');
    $testFile = 'test_' . time() . '.txt';
    
    // Try to write a file
    $disk->put($testFile, 'Test content');
    echo "   ✓ Can write to storage/public\n";
    
    // Check if file exists
    if ($disk->exists($testFile)) {
        echo "   ✓ File write verification successful\n";
        $disk->delete($testFile);
        echo "   ✓ Test file cleaned up\n";
    } else {
        echo "   ✗ File write verification failed\n";
    }
    
    // Check directories
    $directories = ['logos', 'favicons', 'images'];
    foreach ($directories as $dir) {
        $path = $dir;
        if (!$disk->exists($path)) {
            echo "   ℹ Creating directory: {$dir}\n";
            $disk->makeDirectory($path);
        } else {
            echo "   ✓ Directory exists: {$dir}\n";
        }
    }
} catch (\Exception $e) {
    echo "   ✗ Storage test failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 6: Check Log File
echo "6. Checking Log Configuration...\n";
try {
    Log::info('Settings Diagnostic Test - Test Log Entry');
    echo "   ✓ Log file is writable\n";
    echo "   Log channel: " . config('logging.default') . "\n";
} catch (\Exception $e) {
    echo "   ✗ Log test failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 7: Check Mass Assignment Protection
echo "7. Checking Model Fillable Fields...\n";
try {
    $setting = new Setting();
    $fillable = $setting->getFillable();
    echo "   ✓ Fillable fields (" . count($fillable) . "):\n";
    foreach ($fillable as $field) {
        echo "     - {$field}\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Failed to check fillable fields: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 8: Direct SQL Update Test
echo "8. Testing Direct SQL Update...\n";
try {
    $testValue = 'SQL Test ' . time();
    DB::table('settings')->updateOrInsert(
        [], // Empty where clause - will update first or insert
        ['site_name' => $testValue]
    );
    
    $check = DB::table('settings')->value('site_name');
    if ($check === $testValue) {
        echo "   ✓ Direct SQL update successful\n";
        // Restore
        if ($settings) {
            DB::table('settings')->updateOrInsert([], ['site_name' => $settings->site_name]);
        }
    } else {
        echo "   ✗ Direct SQL update failed\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Direct SQL update failed: " . $e->getMessage() . "\n";
}
echo "\n";

echo "==============================================\n";
echo "DIAGNOSTIC COMPLETE\n";
echo "==============================================\n\n";

echo "RECOMMENDATIONS:\n";
echo "- If all tests pass, the issue may be with the frontend/form submission\n";
echo "- Check browser console for JavaScript errors\n";
echo "- Check Laravel logs at storage/logs/laravel.log\n";
echo "- Verify that Inertia.js is properly configured\n";
echo "- Ensure CSRF token is being sent with requests\n\n";

echo "To view detailed logs, run:\n";
echo "  tail -f storage/logs/laravel.log\n\n";
