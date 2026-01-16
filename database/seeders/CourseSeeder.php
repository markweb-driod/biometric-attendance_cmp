<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first department, academic level, and semester
        $department = \App\Models\Department::first();
        $academicLevel = \App\Models\AcademicLevel::first();
        $semester = \App\Models\Semester::where('is_current', true)->first();
        
        if (!$department || !$academicLevel || !$semester) {
            return; // Skip if required data doesn't exist
        }

        $courses = [
            [
                'course_code' => 'CSC301',
                'course_name' => 'Software Engineering',
                'description' => 'Introduction to software engineering principles',
                'credit_units' => 3,
                'department_id' => $department->id,
                'academic_level_id' => $academicLevel->id,
                'semester_id' => $semester->id,
                'is_active' => true,
            ],
            [
                'course_code' => 'CSC302',
                'course_name' => 'Database Systems',
                'description' => 'Database design and management',
                'credit_units' => 3,
                'department_id' => $department->id,
                'academic_level_id' => $academicLevel->id,
                'semester_id' => $semester->id,
                'is_active' => true,
            ],
            [
                'course_code' => 'CSC303',
                'course_name' => 'Computer Networks',
                'description' => 'Network protocols and architecture',
                'credit_units' => 3,
                'department_id' => $department->id,
                'academic_level_id' => $academicLevel->id,
                'semester_id' => $semester->id,
                'is_active' => true,
            ],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['course_code' => $course['course_code']],
                $course
            );
        }
    }
}
