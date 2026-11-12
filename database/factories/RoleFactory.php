<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'slug' => fn (array $attributes): string => Str::slug($attributes['name']),
            'description' => fake()->sentence(),
            'is_system' => false,
        ];
    }
}
