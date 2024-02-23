<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\User;
use App\Models\ScheduleRule;
use App\Enums\RecurringTimeDay;
use App\Enums\RecurringType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduleRule>
 */
class ScheduleRuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ScheduleRule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'recurring_type' => $this->faker->randomElement([RecurringType::DAILY->value, RecurringType::WEEKLY->value, RecurringType::MONTHLY->value, RecurringType::YEARLY->value]),
            'recurring_type_duration' => 3,
            'recurring_start_time' => $this->faker->time('H:i'),
            'recurring_end_time' => $this->faker->time('H:i'),
            'recurring_start_lunch_time' => '12:00',
            'recurring_end_lunch_time' => '13:00',
            'recurring_ignore_weekends' => $this->faker->boolean,
            'recurring_duration_minutes' => $this->faker->numberBetween(30, 60),
            'recurring_interval_minutes' => $this->faker->numberBetween(5, 10),
            'recurring_has_lunch_time' => 1,
            'recurring_has_weekdays' => $this->faker->boolean,
            'recurring_weekdays' => $this->faker->randomElements(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], 2),
            'recurring_duration_start_date' => $this->faker->date,
            'custom_date_times' => json_encode($this->faker->randomElement([
                ['start_datetime' => '2022-03-01 09:00:00', 'end_datetime' => '2022-03-01 12:00:00'],
                ['start_datetime' => '2022-03-02 13:00:00', 'end_datetime' => '2022-03-02 16:00:00'],
            ])),
            'is_recurring' => $this->faker->boolean,
            'is_custom' => 0,
        ];
    }
}
