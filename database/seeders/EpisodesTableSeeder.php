<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class EpisodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('episodes')->insert([
            [
                'name' => 'Episode 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Episode 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Episode 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
