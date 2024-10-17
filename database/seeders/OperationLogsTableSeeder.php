<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class OperationLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data for operation logs
        $logs = [
            [
                'operation' => 'add',
                'episode_id' => 1,
                'part_id' => 1,
                'position' => 1,
                'timestamp' => now(),
                'status' => 1, // Assuming 1 for 'completed'
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'operation' => 'delete',
                'episode_id' => 1,
                'part_id' => 2,
                'position' => 2,
                'timestamp' => now(),
                'status' => 0, // Assuming 0 for 'pending'
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more logs as needed
        ];

        // Insert data into the operation_logs table
        DB::table('operation_logs')->insert($logs);
    }
}
