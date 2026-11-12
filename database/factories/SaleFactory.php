<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'created_by' => User::factory(),
            'quantity' => 1,
            'unit_price' => 10,
            'total' => 10,
            'sold_at' => now(),
        ];
    }
}
