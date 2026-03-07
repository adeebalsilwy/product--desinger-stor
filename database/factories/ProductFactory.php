<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'product_type_id' => \App\Models\ProductType::factory(),
            'name' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'description' => $this->faker->paragraph,
            'sku' => $this->faker->uuid,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'inventory_count' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}
