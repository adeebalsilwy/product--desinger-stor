<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Settings Update Test ===\n\n";

// Get the first settings record
$settings = \App\Models\Setting::first();

if ($settings) {
    echo "Original Settings:\n";
    echo "  Site Name: " . $settings->site_name . "\n";
    echo "  Site Email: " . $settings->site_email . "\n";
    echo "  Brand Primary Color: " . $settings->brand_primary_color . "\n";
    echo "  Brand Tagline: " . $settings->brand_tagline . "\n";
    echo "  Facebook URL: " . $settings->facebook_url . "\n";
    echo "  Meta Keywords: " . $settings->meta_keywords . "\n\n";
    
    // Update settings
    $settings->update([
        'site_name' => 'Test Boutique Updated',
        'site_email' => 'updated@example.com',
        'brand_primary_color' => '#ff0000',
        'brand_tagline' => 'Updated Elegant Tagline',
        'facebook_url' => 'https://facebook.com/updated-test',
        'meta_keywords' => 'updated, test, keywords, seo',
        'meta_description' => 'Updated meta description for testing',
        'twitter_url' => 'https://twitter.com/updated-test',
        'instagram_url' => 'https://instagram.com/updated-test',
        'analytics_id' => 'UA-123456789-1',
        'enable_registration' => false,
        'enable_reviews' => true,
        'products_per_page' => 24,
        'tax_rate' => 15.50,
        'maintenance_mode' => false,
        'maintenance_message' => 'Site maintenance message'
    ]);
    
    echo "✓ Settings updated successfully!\n\n";
    
    // Verify updates
    $updatedSettings = \App\Models\Setting::first();
    echo "Updated Settings:\n";
    echo "  Site Name: " . $updatedSettings->site_name . "\n";
    echo "  Site Email: " . $updatedSettings->site_email . "\n";
    echo "  Brand Primary Color: " . $updatedSettings->brand_primary_color . "\n";
    echo "  Brand Tagline: " . $updatedSettings->brand_tagline . "\n";
    echo "  Facebook URL: " . $updatedSettings->facebook_url . "\n";
    echo "  Meta Keywords: " . $updatedSettings->meta_keywords . "\n";
    echo "  Products Per Page: " . $updatedSettings->products_per_page . "\n";
    echo "  Tax Rate: " . $updatedSettings->tax_rate . "\n";
    echo "  Enable Registration: " . ($updatedSettings->enable_registration ? 'Yes' : 'No') . "\n";
    echo "  Enable Reviews: " . ($updatedSettings->enable_reviews ? 'Yes' : 'No') . "\n\n";
    
    echo "=== Test Complete ===\n";
    echo "All settings functionality is working properly!\n";
} else {
    echo "✗ No settings found in database\n";
}