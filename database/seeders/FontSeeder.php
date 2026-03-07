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
            [
                'name' => 'Arial',
                'display_name' => 'Arial',
                'font_file_url' => '/fonts/arial.ttf',
                'license_type' => 'free',
                'category' => 'sans-serif',
                'is_active' => true,
            ],
            [
                'name' => 'Times New Roman',
                'display_name' => 'Times New Roman',
                'font_file_url' => '/fonts/times-new-roman.ttf',
                'license_type' => 'free',
                'category' => 'serif',
                'is_active' => true,
            ],
            [
                'name' => 'Courier New',
                'display_name' => 'Courier New',
                'font_file_url' => '/fonts/courier-new.ttf',
                'license_type' => 'free',
                'category' => 'monospace',
                'is_active' => true,
            ],
            [
                'name' => 'Georgia',
                'display_name' => 'Georgia',
                'font_file_url' => '/fonts/georgia.ttf',
                'license_type' => 'free',
                'category' => 'serif',
                'is_active' => true,
            ],
            [
                'name' => 'Verdana',
                'display_name' => 'Verdana',
                'font_file_url' => '/fonts/verdana.ttf',
                'license_type' => 'free',
                'category' => 'sans-serif',
                'is_active' => true,
            ],
            [
                'name' => 'Comic Sans MS',
                'display_name' => 'Comic Sans MS',
                'font_file_url' => '/fonts/comic-sans.ttf',
                'license_type' => 'free',
                'category' => 'decorative',
                'is_active' => true,
            ],
            [
                'name' => 'Impact',
                'display_name' => 'Impact',
                'font_file_url' => '/fonts/impact.ttf',
                'license_type' => 'free',
                'category' => 'decorative',
                'is_active' => true,
            ],
            [
                'name' => 'Dancing Script',
                'display_name' => 'Dancing Script',
                'font_file_url' => '/fonts/dancing-script.ttf',
                'license_type' => 'commercial',
                'category' => 'script',
                'is_active' => true,
            ],
            [
                'name' => 'Pacifico',
                'display_name' => 'Pacifico',
                'font_file_url' => '/fonts/pacifico.ttf',
                'license_type' => 'free',
                'category' => 'script',
                'is_active' => true,
            ],
            [
                'name' => 'Lobster',
                'display_name' => 'Lobster',
                'font_file_url' => '/fonts/lobster.ttf',
                'license_type' => 'commercial',
                'category' => 'decorative',
                'is_active' => true,
            ],
        ];
        
        foreach ($fonts as $font) {
            Font::create($font);
        }
    }
}
