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
        if (!Superadmin::where('email', 'admin@cmp.com')->exists()) {
            Superadmin::create([
                'name' => 'Admin',
                'email' => 'admin@cmp.com',
                'password' => Hash::make('123456'),
            ]);
        }
    }
} 