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
        // التحقق مما إذا كانت الإعدادات موجودة بالفعل لتجنب التكرار
        if (!Setting::exists()) {
            Setting::create([
                'site_name' => 'أزياء يمنية أصيلة',
                'site_logo' => '/template/yemeni-logo.png',
                'site_favicon' => '/template/yemeni-favicon.ico',
                'site_description' => 'متجر إلكتروني متخصص في الأزياء اليمنية التقليدية والحديثة - أصالة التراث اليمني',
                'site_email' => 'info@yemenifashion.com',
                'site_phone' => '+967-1-234567',
                'site_address' => 'صنعاء، اليمن - شارع الزبيري',
                'site_currency' => 'ريال يمني',
                'tax_rate' => 0.00,
                'maintenance_mode' => false,
                'maintenance_message' => 'نحن نقوم حالياً بتحديث موقعنا. يرجى العودة قريباً!',
                'facebook_url' => 'https://facebook.com/yemenifashion',
                'twitter_url' => 'https://twitter.com/yemenifashion',
                'instagram_url' => 'https://instagram.com/yemenifashion',
                'linkedin_url' => 'https://linkedin.com/company/yemenifashion',
                'meta_keywords' => 'أزياء يمنية, ملابس تقليدية, تراث يمني, تطريز يمني, عبايات, فساتين يمنية, صنعاء, تعز, لحج, حضرموت',
                'meta_description' => 'أزياء يمنية أصيلة - متجر إلكتروني متخصص في الأزياء اليمنية التقليدية والحديثة بأعلى جودة',
                'analytics_id' => null,
                'enable_registration' => true,
                'enable_reviews' => true,
                'products_per_page' => 12,
                'brand_primary_color' => '#8B4513', // بني تراثي
                'brand_secondary_color' => '#DAA520', // ذهبي
                'brand_accent_color' => '#DC143C', // أحمر قاني
                'brand_text_color' => '#000000', // أسود
                'brand_bg_color' => '#f5e6d3', // بيج فاتح
                'brand_logo_woman' => '/template/yemeni-woman-logo.png',
                'brand_tagline' => 'أصالة التراث اليمني بأناقة عصرية',
                'brand_script_font' => 'yemeni-script',
                'brand_regular_font' => 'traditional-arabic',
            ]);
        }
    }
}