<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\EpisodesTableSeeder;
use Database\Seeders\PartsTableSeeder;
use Database\Seeders\OperationLogsTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EpisodesTableSeeder::class,
            PartsTableSeeder::class,
            OperationLogsTableSeeder::class
        ]);
    }
}
