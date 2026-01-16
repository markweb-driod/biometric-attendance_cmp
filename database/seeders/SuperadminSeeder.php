<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Superadmin;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Superadmin::updateOrCreate(
            ['email' => 'admin@cmp.com'],
            [
                'username' => 'admin',
                'full_name' => 'Super Admin',
                'password' => Hash::make('123456'),
                'is_active' => true,
            ]
        );
    }
}