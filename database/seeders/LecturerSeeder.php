<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Hash;

class LecturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lecturer::updateOrCreate(
            ['email' => 'john.doe@nsuk.edu.ng'],
            [
                'staff_id' => 'LEC001',
                'full_name' => 'Dr. John Doe',
                'password' => Hash::make('password123'),
                'department' => 'Computer Science',
                'is_active' => true,
            ]
        );

        Lecturer::updateOrCreate(
            ['email' => 'jane.smith@nsuk.edu.ng'],
            [
                'staff_id' => 'LEC002',
                'full_name' => 'Prof. Jane Smith',
                'password' => Hash::make('password123'),
                'department' => 'Computer Science',
                'is_active' => true,
            ]
        );

        Lecturer::updateOrCreate(
            ['email' => 'michael.johnson@nsuk.edu.ng'],
            [
                'staff_id' => 'LEC003',
                'full_name' => 'Dr. Michael Johnson',
                'password' => Hash::make('password123'),
                'department' => 'Computer Science',
                'is_active' => true,
            ]
        );
    }
}