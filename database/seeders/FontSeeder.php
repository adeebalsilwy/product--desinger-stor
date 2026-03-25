<?php

namespace Database\Seeders;

use App\Models\Font;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FontSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fonts = [
            // خطوط عربية أصيلة
            [
                'name' => 'Arial',
                'display_name' => 'أريال',
                'font_file_url' => '/fonts/arial.ttf',
                'license_type' => 'free',
                'category' => 'sans-serif',
                'is_active' => true,
            ],
            [
                'name' => 'Traditional Arabic',
                'display_name' => 'عربي تقليدي',
                'font_file_url' => '/fonts/traditional-arabic.ttf',
                'license_type' => 'free',
                'category' => 'traditional',
                'is_active' => true,
            ],
            [
                'name' => 'Naskh',
                'display_name' => 'نسخ',
                'font_file_url' => '/fonts/naskh.ttf',
                'license_type' => 'free',
                'category' => 'calligraphy',
                'is_active' => true,
            ],
            [
                'name' => 'Thuluth',
                'display_name' => 'ثلث',
                'font_file_url' => '/fonts/thuluth.ttf',
                'license_type' => 'commercial',
                'category' => 'calligraphy',
                'is_active' => true,
            ],
            [
                'name' => 'Diwani',
                'display_name' => 'ديواني',
                'font_file_url' => '/fonts/diwani.ttf',
                'license_type' => 'commercial',
                'category' => 'calligraphy',
                'is_active' => true,
            ],
            [
                'name' => 'Kufi',
                'display_name' => 'كوفي',
                'font_file_url' => '/fonts/kufi.ttf',
                'license_type' => 'free',
                'category' => 'traditional',
                'is_active' => true,
            ],
            [
                'name' => 'Andalusian',
                'display_name' => 'أندلسي',
                'font_file_url' => '/fonts/andalusian.ttf',
                'license_type' => 'free',
                'category' => 'historical',
                'is_active' => true,
            ],
            [
                'name' => 'Yemeni Script',
                'display_name' => 'خط يمني',
                'font_file_url' => '/fonts/yemeni-script.ttf',
                'license_type' => 'commercial',
                'category' => 'traditional',
                'is_active' => true,
            ],
            [
                'name' => 'Sanaani Decorative',
                'display_name' => 'صنعاني مزخرف',
                'font_file_url' => '/fonts/sanaani-decorative.ttf',
                'license_type' => 'commercial',
                'category' => 'decorative',
                'is_active' => true,
            ],
            [
                'name' => 'Modern Arabic',
                'display_name' => 'عربي حديث',
                'font_file_url' => '/fonts/modern-arabic.ttf',
                'license_type' => 'free',
                'category' => 'modern',
                'is_active' => true,
            ],
            [
                'name' => 'Ruqaa',
                'display_name' => 'رقعة',
                'font_file_url' => '/fonts/ruqaa.ttf',
                'license_type' => 'free',
                'category' => 'calligraphy',
                'is_active' => true,
            ],
            [
                'name' => 'Maghrebi',
                'display_name' => 'مغربي',
                'font_file_url' => '/fonts/maghrebi.ttf',
                'license_type' => 'free',
                'category' => 'traditional',
                'is_active' => true,
            ],
        ];
        
        foreach ($fonts as $font) {
            Font::create($font);
        }
    }
}
