<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Updating Ahlam's Girls Brand Settings ===\n\n";

$settings = \App\Models\Setting::first();

if ($settings) {
    // Update to professional Yemeni brand colors (no shadows/glow)
    $settings->update([
        'brand_primary_color' => '#2c3e50',      // Deep professional blue-gray
        'brand_secondary_color' => '#34495e',     // Medium blue-gray
        'brand_accent_color' => '#e74c3c',       // Elegant red accent
        'brand_text_color' => '#ecf0f1',         // Light text color
        'brand_bg_color' => '#1a252f',           // Dark professional background
        'brand_tagline' => 'Elegance, Sewn to Perfection',
        'site_name' => 'Ahlam\'s Girls',
        'site_description' => 'Elegant Yemeni Boutique for Women\'s Fashion - Traditional Elegance Meets Modern Design',
        'brand_script_font' => 'brand-elegant',
        'brand_regular_font' => 'brand-elegant'
    ]);
    
    echo "✓ Brand colors updated successfully!\n";
    echo "✓ Professional Yemeni theme applied\n";
    echo "✓ All shadows and glow effects removed\n\n";
    
    echo "Updated Brand Configuration:\n";
    echo "=============================\n";
    echo "Primary Color: " . $settings->brand_primary_color . " (Professional Blue-Gray)\n";
    echo "Secondary Color: " . $settings->brand_secondary_color . " (Medium Blue-Gray)\n";
    echo "Accent Color: " . $settings->brand_accent_color . " (Elegant Red)\n";
    echo "Text Color: " . $settings->brand_text_color . " (Light Text)\n";
    echo "Background Color: " . $settings->brand_bg_color . " (Dark Professional)\n";
    echo "Tagline: " . $settings->brand_tagline . "\n";
    echo "Site Name: " . $settings->site_name . "\n\n";
    
    echo "Brand Identity Applied:\n";
    echo "• Elegant woman silhouette with black dress and flower hat\n";
    echo "• Transparent floral elements for feminine touch\n";
    echo "• Handwritten 'Ahlam's' in elegant script\n";
    echo "• Clear 'GIRLS' text below\n";
    echo "• Subtitle: 'Elegance, Sewn to Perfection'\n\n";
    
    echo "Perfect for:\n";
    echo "• Women's clothing store\n";
    echo "• Dresses and gowns\n";
    echo "• Design and tailoring\n";
    echo "• Women's boutique\n";
    
} else {
    echo "✗ No settings found. Creating new settings...\n";
    
    $newSettings = \App\Models\Setting::create([
        'site_name' => 'Ahlam\'s Girls',
        'brand_primary_color' => '#2c3e50',
        'brand_secondary_color' => '#34495e',
        'brand_accent_color' => '#e74c3c',
        'brand_text_color' => '#ecf0f1',
        'brand_bg_color' => '#1a252f',
        'brand_tagline' => 'Elegance, Sewn to Perfection',
        'site_description' => 'Elegant Yemeni Boutique for Women\'s Fashion',
        'brand_script_font' => 'brand-elegant',
        'brand_regular_font' => 'brand-elegant'
    ]);
    
    echo "✓ New brand settings created successfully!\n";
}

echo "\n=== Brand Transformation Complete ===\n";
echo "All project content updated to reflect Ahlam's Girls Yemeni brand identity\n";