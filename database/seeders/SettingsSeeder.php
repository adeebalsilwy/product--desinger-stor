<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if settings already exist to avoid duplicates
        if (!Setting::exists()) {
            Setting::create([
                'site_name' => 'D-Shirts',
                'site_logo' => null,
                'site_favicon' => null,
                'site_description' => 'Elegant Boutique for Women\'s Fashion',
                'site_email' => 'info@ahlamsgirls.com',
                'site_phone' => '+1234567890',
                'site_address' => '123 Fashion Street, Style City',
                'site_currency' => 'USD',
                'tax_rate' => 0.00,
                'maintenance_mode' => false,
                'maintenance_message' => 'We are currently updating our site. Please check back soon!',
                'facebook_url' => 'https://facebook.com/ahlamsgirls',
                'twitter_url' => 'https://twitter.com/ahlamsgirls',
                'instagram_url' => 'https://instagram.com/ahlamsgirls',
                'linkedin_url' => 'https://linkedin.com/company/ahlamsgirls',
                'meta_keywords' => 'fashion, women, clothing, boutique, elegant',
                'meta_description' => 'Ahlam\'s Girls - Elegant Boutique for Women\'s Fashion',
                'analytics_id' => null,
                'enable_registration' => true,
                'enable_reviews' => true,
                'products_per_page' => 12,
                'brand_primary_color' => '#1a1a2e',
                'brand_secondary_color' => '#16213e',
                'brand_accent_color' => '#e94560',
                'brand_text_color' => '#ffffff',
                'brand_bg_color' => '#0f3460',
                'brand_logo_woman' => null,
                'brand_tagline' => 'Elegance, Sewn to Perfection',
                'brand_script_font' => 'brand-script',
                'brand_regular_font' => 'brand-elegant',
            ]);
        }
    }
}