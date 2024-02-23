<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScheduleRule;

class AddTestingScheduleRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create schedule rules for a photography business
        ScheduleRule::factory()->count(1)->create([
            'title' => 'Photography Session',
            'description' => 'Capture beautiful moments with our professional photography services.',
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '18:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 30,
            'recurring_weekdays' => ['monday', 'wednesday', 'friday'],
            'recurring_duration_start_date' => now()->toDateString(),
            'is_recurring' => true,
        ]);

        // Create schedule rules for dental appointments
        ScheduleRule::factory()->count(1)->create([
            'title' => 'Dental Checkup',
            'description' => 'Ensure your dental health with our professional dental services.',
            'recurring_start_time' => '08:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 45,
            'recurring_interval_minutes' => 15,
            'recurring_weekdays' => ['tuesday', 'thursday'],
            'recurring_duration_start_date' => now()->toDateString(),
            'is_recurring' => true,
        ]);
    }
}
