<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Episode;
use App\Models\Part;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OperationLog>
 */
class OperationLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'operation' => $this->faker->randomElement(['add', 'delete', 'update']), // Random operation type
            'episode_id' => Episode::factory(), // Automatically create an Episode for each OperationLog
            'part_id' => Part::factory(), // Automatically create a Part for each OperationLog
            'position' => $this->faker->numberBetween(1, 100), // Random position between 1 and 100
            'timestamp' => now(), // Set timestamp to current time
            'status' => $this->faker->boolean ? 1 : 0, // Random status (0 or 1)
        ];
    }
}
