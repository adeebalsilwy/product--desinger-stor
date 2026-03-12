<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AhlamsGirlsBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing settings with Ahlam's Girls brand identity
        DB::table('settings')->where('id', 1)->update([
            'site_name' => "Ahlam's Girls",
            'site_description' => 'Elegant women\'s boutique specializing in custom dresses and fashion design',
            'brand_primary_color' => '#2c3e50',
            'brand_secondary_color' => '#34495e',
            'brand_accent_color' => '#e74c3c',
            'brand_bg_color' => '#ecf0f1',
            'brand_text_color' => '#2c3e50',
            'brand_tagline' => 'Elegance, Sewn to Perfection',
            'brand_script_font' => 'brand-script',
            'brand_regular_font' => 'brand-elegant',
            'site_currency' => 'SAR',
            'tax_rate' => '15.00',
            'products_per_page' => 12,
            'enable_registration' => true,
            'enable_reviews' => true,
            'updated_at' => now()
        ]);
        
        // Create brand logo woman setting if it doesn't exist
        $existingBrandLogo = DB::table('settings')->where('brand_logo_woman', '!=', null)->first();
        if (!$existingBrandLogo) {
            DB::table('settings')->where('id', 1)->update([
                'brand_logo_woman' => 'elegant-woman-silhouette',
                'updated_at' => now()
            ]);
        }
        
        $this->command->info('Ahlam\'s Girls brand settings updated successfully!');
    }
}
