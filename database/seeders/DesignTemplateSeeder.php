<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DesignTemplate;
use App\Models\ProductType;
use App\Models\User;

class DesignTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the default product type (t-shirt)
        $productType = ProductType::where('slug', 't-shirt')->first();
        if (!$productType) {
            $productType = ProductType::first();
        }
        
        if ($productType) {
            // Check if templates already exist for this product type to avoid duplicates
            $existingTemplateCount = DesignTemplate::where('product_type_id', $productType->id)->count();
            
            if ($existingTemplateCount == 0) {
                // Create sample templates
                DesignTemplate::create([
                    'name' => 'Basic T-Shirt Template',
                    'description' => 'A classic basic t-shirt design template',
                    'product_type_id' => $productType->id,
                    'category' => 'basic',
                    'tags' => ['basic', 'simple', 'classic'],
                    'thumbnail_url' => '/images/template-thumbs/basic-tshirt.jpg',
                    'preview_url' => '/images/template-previews/basic-tshirt.jpg',
                    'design_data' => [
                        'elements' => [
                            [
                                'type' => 'text',
                                'text' => 'Basic Design',
                                'x' => 150,
                                'y' => 200,
                                'width' => 200,
                                'height' => 40,
                                'fontSize' => 24,
                                'fontFamily' => 'Arial',
                                'color' => '#000000',
                                'rotation' => 0,
                            ]
                        ]
                    ],
                    'is_premium' => false,
                    'price' => 0.00,
                    'usage_count' => 0,
                    'created_by' => User::first()?->id,
                ]);
                
                DesignTemplate::create([
                    'name' => 'Geometric Pattern Template',
                    'description' => 'Modern geometric pattern design',
                    'product_type_id' => $productType->id,
                    'category' => 'pattern',
                    'tags' => ['geometric', 'modern', 'pattern'],
                    'thumbnail_url' => '/images/template-thumbs/geometric-pattern.jpg',
                    'preview_url' => '/images/template-previews/geometric-pattern.jpg',
                    'design_data' => [
                        'elements' => [
                            [
                                'type' => 'shape',
                                'shapeType' => 'triangle',
                                'x' => 100,
                                'y' => 150,
                                'width' => 100,
                                'height' => 100,
                                'color' => '#ff0000',
                                'rotation' => 0,
                            ],
                            [
                                'type' => 'text',
                                'text' => 'Geometry',
                                'x' => 200,
                                'y' => 250,
                                'width' => 150,
                                'height' => 30,
                                'fontSize' => 20,
                                'fontFamily' => 'Arial',
                                'color' => '#0000ff',
                                'rotation' => 15,
                            ]
                        ]
                    ],
                    'is_premium' => false,
                    'price' => 0.00,
                    'usage_count' => 0,
                    'created_by' => User::first()?->id,
                ]);
                
                DesignTemplate::create([
                    'name' => 'Floral Design Template',
                    'description' => 'Beautiful floral design with nature elements',
                    'product_type_id' => $productType->id,
                    'category' => 'floral',
                    'tags' => ['floral', 'nature', 'flowers'],
                    'thumbnail_url' => '/images/template-thumbs/floral-design.jpg',
                    'preview_url' => '/images/template-previews/floral-design.jpg',
                    'design_data' => [
                        'elements' => [
                            [
                                'type' => 'shape',
                                'shapeType' => 'circle',
                                'x' => 120,
                                'y' => 180,
                                'width' => 80,
                                'height' => 80,
                                'color' => '#ff69b4',
                                'rotation' => 0,
                            ],
                            [
                                'type' => 'text',
                                'text' => 'Nature',
                                'x' => 220,
                                'y' => 220,
                                'width' => 120,
                                'height' => 25,
                                'fontSize' => 18,
                                'fontFamily' => 'Georgia',
                                'color' => '#228b22',
                                'rotation' => -10,
                            ]
                        ]
                    ],
                    'is_premium' => true,
                    'price' => 4.99,
                    'usage_count' => 0,
                    'created_by' => User::first()?->id,
                ]);
            }
        }
    }
}