<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'site_name' => "Ahlam's Girls",
            'site_logo' => '/storage/logos/ahlams-girls-logo.png',
            'site_favicon' => '/favicon.ico',
            'site_description' => "Elegance, Sewn to Perfection. Discover exquisite women's fashion with our curated collection of elegant dresses, sophisticated designs, and luxury boutique wear.",
            'site_email' => 'contact@ahlamsgirls.com',
            'site_phone' => '+966 50 123 4567',
            'site_address' => 'Riyadh, Saudi Arabia',
            'site_currency' => 'SAR',
            'tax_rate' => 15.00,
            'maintenance_mode' => false,
            'maintenance_message' => 'We are currently enhancing our boutique experience. Please check back soon.',
            // Brand Colors - Ahlam's Girls Palette
            'brand_primary_color' => '#1a1a2e',      // Deep navy blue
            'brand_secondary_color' => '#16213e',    // Rich navy
            'brand_accent_color' => '#e94560',       // Rose pink
            'brand_bg_color' => '#0f3460',           // Midnight blue
            'brand_text_color' => '#ffffff',         // Pure white
            // Brand Identity
            'brand_logo_woman' => '/storage/logos/elegant-woman-silhouette.svg',
            'brand_tagline' => 'Elegance, Sewn to Perfection',
            'brand_script_font' => 'brand-script',
            'brand_regular_font' => 'brand-elegant',
            // Social Media
            'facebook_url' => 'https://facebook.com/ahlamsgirls',
            'twitter_url' => 'https://twitter.com/ahlamsgirls',
            'instagram_url' => 'https://instagram.com/ahlamsgirls',
            'linkedin_url' => 'https://linkedin.com/company/ahlamsgirls',
            'meta_keywords' => "women fashion, elegant dresses, luxury boutique, Saudi Arabia fashion, designer dresses, custom clothing, women's boutique, Ahlam's Girls",
            'meta_description' => "Ahlam's Girls - Where elegance meets perfection. Discover exquisite women's fashion featuring elegant dresses, sophisticated designs, and luxury boutique wear in Saudi Arabia.",
            'analytics_id' => null,
            'enable_registration' => true,
            'enable_reviews' => true,
            'products_per_page' => 12
        ]);
    }
}
