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
        Lecturer::create([
            'staff_id' => 'LEC001',
            'name' => 'Dr. John Doe',
            'email' => 'john.doe@nsuk.edu.ng',
            'password' => Hash::make('password123'),
            'department' => 'Computer Science',
            'title' => 'Dr.',
            'is_active' => true,
        ]);

        Lecturer::create([
            'staff_id' => 'LEC002',
            'name' => 'Prof. Jane Smith',
            'email' => 'jane.smith@nsuk.edu.ng',
            'password' => Hash::make('password123'),
            'department' => 'Computer Science',
            'title' => 'Prof.',
            'is_active' => true,
        ]);

        Lecturer::create([
            'staff_id' => 'LEC003',
            'name' => 'Dr. Michael Johnson',
            'email' => 'michael.johnson@nsuk.edu.ng',
            'password' => Hash::make('password123'),
            'department' => 'Computer Science',
            'title' => 'Dr.',
            'is_active' => true,
        ]);
    }
} 