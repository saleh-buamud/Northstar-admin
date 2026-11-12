<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'demo_key' => null,
            'name' => fake()->words(3, true),
            'sku' => fake()->unique()->bothify('SKU-####-??'),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 1, 1000),
            'sale_price' => null,
            'stock_quantity' => fake()->numberBetween(0, 100),
            'image_path' => null,
            'status' => 'active',
        ];
    }
}
