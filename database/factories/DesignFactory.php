<?php

namespace Database\Factories;

use App\Models\SavedDesign;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

class DesignFactory extends Factory
{
    protected $model = SavedDesign::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'product_type_id' => ProductType::factory(),
            'name' => $this->faker->sentence,
            'design_data' => [
                'version' => '5.3.0',
                'objects' => [
                    [
                        'type' => 'i-text',
                        'left' => 100,
                        'top' => 100,
                        'text' => $this->faker->sentence,
                        'fontFamily' => 'Arial',
                        'fontSize' => 40,
                        'fill' => '#000000',
                        'id' => 'text-1'
                    ]
                ],
                'background' => '#ffffff',
                'width' => 800,
                'height' => 800
            ],
            'is_public' => false,
            'is_template' => false,
        ];
    }
}
