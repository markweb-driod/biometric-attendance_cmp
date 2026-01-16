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
        // Get the first department
        $department = \App\Models\Department::first();
        
        if (!$department) {
            return; // Skip if department doesn't exist
        }

        $lecturers = [
            [
                'staff_id' => 'LEC001',
                'user_id' => null, // Will be set after user creation
                'phone' => '08012345681',
                'department_id' => $department->id,
                'title' => 'Dr.',
                'is_active' => true,
            ],
            [
                'staff_id' => 'LEC002',
                'user_id' => null,
                'phone' => '08012345682',
                'department_id' => $department->id,
                'title' => 'Prof.',
                'is_active' => true,
            ],
            [
                'staff_id' => 'LEC003',
                'user_id' => null,
                'phone' => '08012345683',
                'department_id' => $department->id,
                'title' => 'Dr.',
                'is_active' => true,
            ],
        ];

        foreach ($lecturers as $lecturerData) {
            // Create user first
            $user = \App\Models\User::create([
                'username' => strtolower(str_replace(['.', ' '], ['', ''], $lecturerData['staff_id'])),
                'email' => strtolower(str_replace(['.', ' '], ['', ''], $lecturerData['staff_id'])) . '@nsuk.edu.ng',
                'full_name' => $lecturerData['title'] . ' ' . $lecturerData['staff_id'],
                'password' => Hash::make('password123'),
                'role' => 'lecturer',
                'is_active' => true,
            ]);

            // Create lecturer with user_id
            $lecturerData['user_id'] = $user->id;
            Lecturer::updateOrCreate(
                ['staff_id' => $lecturerData['staff_id']],
                $lecturerData
            );
        }
    }
}