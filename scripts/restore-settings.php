<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Restoring Settings to Original Values ===\n\n";

// Get the first settings record
$settings = \App\Models\Setting::first();

if ($settings) {
    $settings->update([
        'site_name' => 'Ahlam\'s Girls',
        'site_email' => 'info@ahlamsgirls.com',
        'brand_primary_color' => '#1a1a2e',
        'brand_tagline' => 'Elegance, Sewn to Perfection',
        'facebook_url' => 'https://facebook.com/ahlamsgirls',
        'meta_keywords' => 'fashion, women, clothing, boutique, elegant',
        'site_description' => 'Elegant Boutique for Women\'s Fashion',
        'site_phone' => '+1234567890',
        'site_address' => '123 Fashion Street, Style City',
        'tax_rate' => 0.00,
        'enable_registration' => true,
        'enable_reviews' => true,
        'products_per_page' => 12,
        'site_currency' => 'USD',
        'maintenance_mode' => false,
        'twitter_url' => 'https://twitter.com/ahlamsgirls',
        'instagram_url' => 'https://instagram.com/ahlamsgirls',
        'linkedin_url' => 'https://linkedin.com/company/ahlamsgirls',
        'meta_description' => 'Ahlam\'s Girls - Elegant Boutique for Women\'s Fashion',
        'maintenance_message' => 'We are currently updating our site. Please check back soon!',
    ]);
    
    echo "✓ Settings restored to original values!\n\n";
    
    // Display final settings
    $finalSettings = \App\Models\Setting::first();
    echo "Current Settings Configuration:\n";
    echo "===================================\n";
    echo "Basic Information:\n";
    echo "  Site Name: " . $finalSettings->site_name . "\n";
    echo "  Site Description: " . $finalSettings->site_description . "\n";
    echo "  Site Email: " . $finalSettings->site_email . "\n";
    echo "  Site Phone: " . $finalSettings->site_phone . "\n";
    echo "  Site Address: " . $finalSettings->site_address . "\n";
    echo "  Site Currency: " . $finalSettings->site_currency . "\n";
    echo "  Tax Rate: " . $finalSettings->tax_rate . "%\n\n";
    
    echo "Brand Identity:\n";
    echo "  Brand Primary Color: " . $finalSettings->brand_primary_color . "\n";
    echo "  Brand Secondary Color: " . $finalSettings->brand_secondary_color . "\n";
    echo "  Brand Accent Color: " . $finalSettings->brand_accent_color . "\n";
    echo "  Brand Text Color: " . $finalSettings->brand_text_color . "\n";
    echo "  Brand Background Color: " . $finalSettings->brand_bg_color . "\n";
    echo "  Brand Tagline: " . $finalSettings->brand_tagline . "\n\n";
    
    echo "Social Media:\n";
    echo "  Facebook: " . $finalSettings->facebook_url . "\n";
    echo "  Twitter: " . $finalSettings->twitter_url . "\n";
    echo "  Instagram: " . $finalSettings->instagram_url . "\n";
    echo "  LinkedIn: " . $finalSettings->linkedin_url . "\n\n";
    
    echo "SEO Settings:\n";
    echo "  Meta Keywords: " . $finalSettings->meta_keywords . "\n";
    echo "  Meta Description: " . $finalSettings->meta_description . "\n";
    echo "  Analytics ID: " . $finalSettings->analytics_id . "\n\n";
    
    echo "Store Configuration:\n";
    echo "  Products Per Page: " . $finalSettings->products_per_page . "\n";
    echo "  Enable Registration: " . ($finalSettings->enable_registration ? 'Yes' : 'No') . "\n";
    echo "  Enable Reviews: " . ($finalSettings->enable_reviews ? 'Yes' : 'No') . "\n";
    echo "  Maintenance Mode: " . ($finalSettings->maintenance_mode ? 'Yes' : 'No') . "\n\n";
    
    echo "=== Settings Configuration Complete ===\n";
    echo "All settings are properly configured and ready for testing!\n";
} else {
    echo "✗ No settings found in database\n";
}