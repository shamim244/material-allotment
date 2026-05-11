<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Contractor
        User::updateOrCreate(
            ['email' => 'contractor@example.com'],
            [
                'name' => 'Test Contractor',
                'password' => Hash::make('password'),
                'role' => 'contractor',
            ]
        );

        // Create Worker
        User::updateOrCreate(
            ['email' => 'worker@example.com'],
            [
                'name' => 'Test Worker',
                'password' => Hash::make('password'),
                'role' => 'worker',
            ]
        );
    }
}
