<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Current Settings ===\n\n";

$settings = \App\Models\Setting::first();

if ($settings) {
    echo "Site Name: " . $settings->site_name . "\n";
    echo "Brand Primary Color: " . $settings->brand_primary_color . "\n";
    echo "Brand Secondary Color: " . $settings->brand_secondary_color . "\n";
    echo "Brand Accent Color: " . $settings->brand_accent_color . "\n";
    echo "Brand Text Color: " . $settings->brand_text_color . "\n";
    echo "Brand Background Color: " . $settings->brand_bg_color . "\n";
    echo "Brand Tagline: " . $settings->brand_tagline . "\n";
    echo "Site Logo: " . $settings->site_logo . "\n";
    echo "Brand Logo Woman: " . $settings->brand_logo_woman . "\n";
} else {
    echo "No settings found in database\n";
}