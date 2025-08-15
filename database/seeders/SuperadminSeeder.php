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
                'name' => 'Admin',
                'password' => Hash::make('123456'),
            ]
        );
    }
}