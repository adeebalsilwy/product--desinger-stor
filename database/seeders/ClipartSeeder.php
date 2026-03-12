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
        // Create clipart categories if they don't exist
        $categories = [
            ['name' => 'Logos', 'slug' => 'logos'],
            ['name' => 'Icons', 'slug' => 'icons'],
            ['name' => 'Shapes', 'slug' => 'shapes'],
            ['name' => 'Decorations', 'slug' => 'decorations'],
        ];

        $categoryModels = [];
        foreach ($categories as $category) {
            $categoryModel = ClipartCategory::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
            $categoryModels[$category['slug']] = $categoryModel;
        }

        // Create sample cliparts if they don't exist
        $cliparts = [
            [
                'title' => 'Star Icon',
                'clipart_category_id' => $categoryModels['icons']->id,
                'image_url' => 'https://via.placeholder.com/150/FFD700/000000?text=★',
                'thumbnail_url' => 'https://via.placeholder.com/50/FFD700/000000?text=★',
                'is_premium' => false,
            ],
            [
                'title' => 'Heart Icon',
                'clipart_category_id' => $categoryModels['icons']->id,
                'image_url' => 'https://via.placeholder.com/150/FF0000/FFFFFF?text=♥',
                'thumbnail_url' => 'https://via.placeholder.com/50/FF0000/FFFFFF?text=♥',
                'is_premium' => false,
            ],
            [
                'title' => 'Company Logo',
                'clipart_category_id' => $categoryModels['logos']->id,
                'image_url' => 'https://via.placeholder.com/150/0000FF/FFFFFF?text=LOGO',
                'thumbnail_url' => 'https://via.placeholder.com/50/0000FF/FFFFFF?text=LOGO',
                'is_premium' => false,
            ],
            [
                'title' => 'Circle Shape',
                'clipart_category_id' => $categoryModels['shapes']->id,
                'image_url' => 'https://via.placeholder.com/150/00FF00/000000?text=●',
                'thumbnail_url' => 'https://via.placeholder.com/50/00FF00/000000?text=●',
                'is_premium' => false,
            ],
            [
                'title' => 'Floral Decoration',
                'clipart_category_id' => $categoryModels['decorations']->id,
                'image_url' => 'https://via.placeholder.com/150/FF69B4/FFFFFF?text=✿',
                'thumbnail_url' => 'https://via.placeholder.com/50/FF69B4/FFFFFF?text=✿',
                'is_premium' => true,
                'price' => 2.99,
            ],
        ];

        foreach ($cliparts as $clipart) {
            Clipart::firstOrCreate(
                ['title' => $clipart['title']],
                $clipart
            );
        }
    }
}