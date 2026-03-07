<?php

namespace Database\Factories;

use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductTypeFactory extends Factory
{
    protected $model = ProductType::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'description' => $this->faker->sentence,
            'base_price' => $this->faker->randomFloat(2, 10, 100),
            'is_active' => true,
        ];
    }
}
