<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Part;
use App\Models\Episode;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Part>
 */
class PartFactory extends Factory
{
    protected $model = Part::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'episode_id' => Episode::factory(), // Automatically create an Episode for each Part
            'position' => $this->faker->numberBetween(1, 100), // Random position between 1 and 100
            'created_at' => now(), // Set created_at to current timestamp
            'updated_at' => now(), // Set updated_at to current timestamp
        ];
    }
}
