<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\AcademicLevel;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first department and academic level
        $department = Department::first();
        $academicLevel = AcademicLevel::first();

        if (!$department || !$academicLevel) {
            return; // Skip if required data doesn't exist
        }

        $students = [
            [
                'matric_number' => 'CSC/2021/001',
                'user_id' => null, // Will be set after user creation
                'phone' => '08012345678',
                'department_id' => $department->id,
                'academic_level_id' => $academicLevel->id,
                'is_active' => true,
            ],
            [
                'matric_number' => 'CSC/2021/002',
                'user_id' => null,
                'phone' => '08012345679',
                'department_id' => $department->id,
                'academic_level_id' => $academicLevel->id,
                'is_active' => true,
            ],
            [
                'matric_number' => 'CSC/2021/003',
                'user_id' => null,
                'phone' => '08012345680',
                'department_id' => $department->id,
                'academic_level_id' => $academicLevel->id,
                'is_active' => true,
            ],
        ];

        foreach ($students as $studentData) {
            // Create user first
            $user = User::create([
                'username' => strtolower(str_replace(['/', ' '], ['', '.'], $studentData['matric_number'])),
                'email' => strtolower(str_replace(['/', ' '], ['', '.'], $studentData['matric_number'])) . '@nsuk.edu.ng',
                'full_name' => 'Student ' . $studentData['matric_number'],
                'password' => bcrypt('student123'),
                'role' => 'student',
                'is_active' => true,
            ]);

            // Create student with user_id
            $studentData['user_id'] = $user->id;
            Student::updateOrCreate(
                ['matric_number' => $studentData['matric_number']],
                $studentData
            );
        }
    }
}
