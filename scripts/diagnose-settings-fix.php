<?php

/**
 * Settings Update Diagnostic Tool
 * This script verifies that all fixes are in place
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\File;

echo "===========================================\n";
echo "  SETTINGS UPDATE DIAGNOSTIC TOOL\n";
echo "===========================================\n\n";

$allPassed = true;

// Test 1: Check .env configuration
echo "1. Checking .env configuration...\n";
$envContent = file_get_contents('.env');
if (strpos($envContent, 'APP_LOCALE=en') !== false && 
    strpos($envContent, 'APP_NAME="Ahlam\'s Girls"') !== false) {
    echo "   ✅ PASS: Environment configured correctly\n";
} else {
    echo "   ❌ FAIL: Environment not configured properly\n";
    $allPassed = false;
}

// Test 2: Check log file status
echo "\n2. Checking log file status...\n";
$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    if (preg_match('/[\x{4e00}-\x{9fff}]/u', $logContent)) {
        echo "   ❌ FAIL: Log file contains garbled characters!\n";
        echo "   → Deleting corrupted log file...\n";
        unlink($logPath);
        echo "   → Log file deleted. Will be recreated on next request.\n";
        $allPassed = false;
    } else {
        echo "   ✅ PASS: Log file is clean (no garbled characters)\n";
    }
} else {
    echo "   ✅ PASS: No log file exists (will be created fresh)\n";
}

// Test 3: Check SettingsController modifications
echo "\n3. Checking SettingsController modifications...\n";
$controllerPath = app_path('Http/Controllers/Admin/SettingsController.php');
$controllerContent = file_get_contents($controllerPath);

$checks = [
    'Log corruption detection' => 'preg_match(\'/[\x{4e00}-\x{9fff}]/u\', $logContent)',
    'Validation without custom messages' => '$validated = $request->validate([',
    'English logging' => '\\Log::info(\'=== Settings Update Started ===\')',
];

foreach ($checks as $name => $pattern) {
    if (strpos($controllerContent, $pattern) !== false) {
        echo "   ✅ PASS: {$name} present\n";
    } else {
        echo "   ❌ FAIL: {$name} missing\n";
        $allPassed = false;
    }
}

// Test 4: Check for custom validation messages removal
echo "\n4. Checking custom validation messages removal...\n";
if (strpos($controllerContent, "'site_name.required' => 'The site name field is required.'") === false) {
    echo "   ✅ PASS: Custom validation messages removed\n";
} else {
    echo "   ❌ FAIL: Custom validation messages still present\n";
    $allPassed = false;
}

// Test 5: Verify cache status
echo "\n5. Verifying cache status...\n";
$configCache = base_path('bootstrap/cache/config.php');
$routeCache = base_path('bootstrap/cache/routes-v7.php');
$viewCacheExists = false;

$cachedFound = false;
if (file_exists($configCache)) {
    echo "   ⚠️  WARNING: Config cache exists. Run: php artisan config:clear\n";
    $cachedFound = true;
}

if (file_exists($routeCache)) {
    echo "   ⚠️  WARNING: Route cache exists. Run: php artisan route:clear\n";
    $cachedFound = true;
}

$views = glob(storage_path('framework/views/*.php'));
if (count($views) > 0) {
    // View cache is normal with Vite, just warn but don't fail
    echo "   ℹ️  INFO: View cache detected (normal with Vite)\n";
}

if (!$cachedFound) {
    echo "   ✅ PASS: Critical caches cleared\n";
} else {
    echo "   ⚠️  Note: Clear config/route caches if experiencing issues\n";
    $allPassed = false; // Still mark as not fully passed but less severe
}

// Test 6: Check database connection
echo "\n6. Checking database connection...\n";
try {
    $settings = \App\Models\Setting::first();
    if ($settings) {
        echo "   ✅ PASS: Database connected, settings record exists\n";
        echo "      - Site Name: " . ($settings->site_name ?? 'null') . "\n";
        echo "      - Products Per Page: " . ($settings->products_per_page ?? 'null') . "\n";
    } else {
        echo "   ⚠️  WARNING: Database connected but no settings record found\n";
        echo "      → Run: php artisan db:seed --class=SettingsTableSeeder\n";
        $allPassed = false;
    }
} catch (\Exception $e) {
    echo "   ❌ FAIL: Database connection failed\n";
    echo "      Error: " . $e->getMessage() . "\n";
    $allPassed = false;
}

// Test 7: Check file permissions
echo "\n7. Checking file permissions...\n";
$storagePath = storage_path();
if (is_writable($storagePath)) {
    echo "   ✅ PASS: Storage directory is writable\n";
} else {
    echo "   ❌ FAIL: Storage directory is not writable\n";
    $allPassed = false;
}

if (is_writable(storage_path('logs'))) {
    echo "   ✅ PASS: Logs directory is writable\n";
} else {
    echo "   ❌ FAIL: Logs directory is not writable\n";
    $allPassed = false;
}

// Test 8: Check for browser extension interference
echo "\n8. Browser Extension Check...\n";
echo "   ℹ️  INFO: If you see 'Feature is disabled' in console,\n";
echo "      this is from Chrome extensions and can be safely ignored.\n";
echo "      It does NOT affect settings functionality.\n";

// Final Summary
echo "\n===========================================\n";
echo "  DIAGNOSTIC SUMMARY\n";
echo "===========================================\n";

if ($allPassed) {
    echo "\n✅ ALL TESTS PASSED!\n";
    echo "\nYour settings update system is ready to use.\n";
    echo "Navigate to: http://127.0.0.1:8000/admin/settings\n";
    echo "\nExpected behavior:\n";
    echo "- Form submits without validation errors\n";
    echo "- All logs appear in clear English\n";
    echo "- No garbled/Chinese characters\n";
    echo "- Settings save to database successfully\n";
} else {
    echo "\n❌ SOME TESTS FAILED!\n";
    echo "\nPlease address the issues above before testing.\n";
    echo "\nRecommended actions:\n";
    echo "1. Clear all caches:\n";
    echo "   php artisan config:clear\n";
    echo "   php artisan cache:clear\n";
    echo "   php artisan route:clear\n";
    echo "   php artisan view:clear\n";
    echo "\n2. If log file was corrupted, it has been deleted.\n";
    echo "   It will be recreated on next settings update.\n";
    echo "\n3. Test settings update again.\n";
}

echo "\n===========================================\n";
echo "Diagnostic completed at: " . date('Y-m-d H:i:s') . "\n";
echo "===========================================\n\n";

exit($allPassed ? 0 : 1);
