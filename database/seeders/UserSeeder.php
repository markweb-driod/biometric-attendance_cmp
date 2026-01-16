<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::updateOrCreate(
            ['email' => 'admin@cmp.com'],
            [
                'username' => 'admin',
                'full_name' => 'System Administrator',
                'password' => Hash::make('123456'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );

        // Create additional test users
        User::updateOrCreate(
            ['email' => 'test@biometric.com'],
            [
                'username' => 'testuser',
                'full_name' => 'Test User',
                'password' => Hash::make('test123'),
                'role' => 'student',
                'is_active' => true,
            ]
        );
    }
}
