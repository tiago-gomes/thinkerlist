<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class PartsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Sample data for parts
         $parts = [
            [
                'episode_id' => 1,
                'position' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'episode_id' => 1,
                'position' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'episode_id' => 1,
                'position' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'episode_id' => 1,
                'position' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'episode_id' => 1,
                'position' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'episode_id' => 1,
                'position' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'episode_id' => 1,
                'position' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'episode_id' => 1,
                'position' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'episode_id' => 1,
                'position' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'episode_id' => 1,
                'position' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'episode_id' => 2,
                'position' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'episode_id' => 2,
                'position' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data into the parts table
        DB::table('parts')->insert($parts);
    }
}
