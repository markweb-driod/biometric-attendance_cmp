<?php

namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Removed User seeding as there is no users table

        // Run our custom seeders
        $this->call([
            LecturerSeeder::class,
            SampleDataSeeder::class,
            SuperadminSeeder::class,
        ]);
    }
}
