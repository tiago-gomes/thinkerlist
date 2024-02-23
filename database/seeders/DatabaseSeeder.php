<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment('local')) {
            $this->call(AddTestingUsersSeeder::class);
            $this->call(AddTestingScheduleRulesSeeder::class);
        }
    }
}
