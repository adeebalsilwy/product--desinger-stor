<?php

namespace Database\Seeders;

use App\Models\Clipart;
use App\Models\ClipartCategory;
use Illuminate\Database\Seeder;

class ClipartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء فئات الزخارف اليمنية
        $categories = [
            ['name' => 'زخارف يمنية', 'slug' => 'yemeni-ornaments'],
            ['name' => 'نقوش تراثية', 'slug' => 'traditional-patterns'],
            ['name' => 'أشكال إسلامية', 'slug' => 'islamic-shapes'],
            ['name' => 'تطريزات', 'slug' => 'embroidery'],
        ];

        $categoryModels = [];
        foreach ($categories as $category) {
            $categoryModel = ClipartCategory::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
            $categoryModels[$category['slug']] = $categoryModel;
        }

        // إنشاء زخارف يمنية أصيلة
        $cliparts = [
            [
                'title' => 'نجمة يمنية ثمانية',
                'clipart_category_id' => $categoryModels['yemeni-ornaments']->id,
                'image_url' => asset('template/yemeni-star.png'),
                'thumbnail_url' => asset('template/thumbnails/yemeni-star-thumb.png'),
                'is_premium' => false,
            ],
            [
                'title' => 'زهرة ياسمين يمنية',
                'clipart_category_id' => $categoryModels['yemeni-ornaments']->id,
                'image_url' => asset('template/yemeni-flower.png'),
                'thumbnail_url' => asset('template/thumbnails/yemeni-flower-thumb.png'),
                'is_premium' => false,
            ],
            [
                'title' => 'نقش لحجي تقليدي',
                'clipart_category_id' => $categoryModels['traditional-patterns']->id,
                'image_url' => asset('template/lahij-pattern.png'),
                'thumbnail_url' => asset('template/thumbnails/lahij-pattern-thumb.png'),
                'is_premium' => true,
                'price' => 4.99,
            ],
            [
                'title' => 'زخرفة صنعانية ذهبية',
                'clipart_category_id' => $categoryModels['yemeni-ornaments']->id,
                'image_url' => asset('template/sanaa-gold.png'),
                'thumbnail_url' => asset('template/thumbnails/sanaa-gold-thumb.png'),
                'is_premium' => true,
                'price' => 6.99,
            ],
            [
                'title' => 'شكل محراب إسلامي',
                'clipart_category_id' => $categoryModels['islamic-shapes']->id,
                'image_url' => asset('template/mihrab.png'),
                'thumbnail_url' => asset('template/thumbnails/mihrab-thumb.png'),
                'is_premium' => false,
            ],
            [
                'title' => 'تطريز تعزي فضي',
                'clipart_category_id' => $categoryModels['embroidery']->id,
                'image_url' => asset('template/taiz-silver.png'),
                'thumbnail_url' => asset('template/thumbnails/taiz-silver-thumb.png'),
                'is_premium' => true,
                'price' => 5.99,
            ],
            [
                'title' => 'نخلة يمنية أصيلة',
                'clipart_category_id' => $categoryModels['yemeni-ornaments']->id,
                'image_url' => asset('template/palm-tree.png'),
                'thumbnail_url' => asset('template/thumbnails/palm-tree-thumb.png'),
                'is_premium' => false,
            ],
            [
                'title' => 'قمر يمني منقوش',
                'clipart_category_id' => $categoryModels['islamic-shapes']->id,
                'image_url' => asset('template/yemeni-moon.png'),
                'thumbnail_url' => asset('template/thumbnails/yemeni-moon-thumb.png'),
                'is_premium' => false,
            ],
            [
                'title' => 'زخرفة حضرمية ملونة',
                'clipart_category_id' => $categoryModels['traditional-patterns']->id,
                'image_url' => asset('template/hadhramaut-pattern.png'),
                'thumbnail_url' => asset('template/thumbnails/hadhramaut-pattern-thumb.png'),
                'is_premium' => true,
                'price' => 7.99,
            ],
            [
                'title' => 'تطريز بدوي تقليدي',
                'clipart_category_id' => $categoryModels['embroidery']->id,
                'image_url' => asset('template/bedouin-embroidery.png'),
                'thumbnail_url' => asset('template/thumbnails/bedouin-embroidery-thumb.png'),
                'is_premium' => true,
                'price' => 8.99,
            ],
        ];

        foreach ($cliparts as $clipart) {
            Clipart::firstOrCreate(
                ['title' => $clipart['title']],
                $clipart
            );
        }
        
        $this->command->info('تم تحميل ' . count($cliparts) . ' زخرفة يمنية بنجاح.');
    }
}