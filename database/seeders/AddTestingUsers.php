<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\Role;

class AddTestingUsers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can customize attributes for the users as needed
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => '12345',
            'role' => Role::ADMIN->value
        ]);

        // Create and insert a specific number of testing manager users
        User::factory()->count(10)->create();
    }
}
