<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Episode;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Episode>
 */
class EpisodeFactory extends Factory
{
    protected $model = Episode::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3), // Generates a random sentence for the name
            'created_at' => now(),                // Set created_at to current timestamp
            'updated_at' => now(),                // Set updated_at to current timestamp
        ];
    }
}
