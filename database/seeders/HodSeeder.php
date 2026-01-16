<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hod;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class HodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first department
        $department = Department::first();
        
        if (!$department) {
            $this->command->error('No departments found. Please run DepartmentSeeder first.');
            return;
        }

        // Create HOD user (use updateOrCreate to avoid duplicates)
        $user = User::updateOrCreate(
            ['username' => 'hod001'],
            [
                'full_name' => 'Dr. John Smith',
                'email' => 'hod@example.com',
                'password' => Hash::make('password123'),
                'role' => 'hod',
                'is_active' => true,
            ]
        );

        // Create HOD record (use updateOrCreate to avoid duplicates)
        Hod::updateOrCreate(
            ['staff_id' => 'HOD001'],
            [
                'user_id' => $user->id,
                'department_id' => $department->id,
                'title' => 'Dr.',
                'phone' => '+2348012345678',
                'office_location' => 'Block A, Room 101',
                'is_active' => true,
                'appointed_at' => now()->subMonths(6),
                'permissions' => [
                    'view_dashboard',
                    'manage_students',
                    'manage_lecturers',
                    'view_reports',
                    'manage_exam_eligibility',
                    'view_audit_logs',
                ],
            ]
        );

        $this->command->info('HOD created successfully!');
        $this->command->info('Email: hod@example.com');
        $this->command->info('Password: password123');
        $this->command->info('Staff ID: HOD001');
    }
}