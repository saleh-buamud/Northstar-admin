<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'action' => 'product.created',
            'subject_type' => null,
            'subject_id' => null,
            'properties' => [],
            'ip_address' => null,
        ];
    }
}
