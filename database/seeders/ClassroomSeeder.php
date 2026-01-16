<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classroom;
use App\Models\Lecturer;
use App\Models\Course;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecturer = Lecturer::first();
        $course = Course::first();

        if ($lecturer && $course) {
            Classroom::updateOrCreate(
                ['pin' => 'CSC301-2024'],
                [
                    'class_name' => 'Software Engineering Class',
                    'course_id' => $course->id,
                    'lecturer_id' => $lecturer->id,
                    'pin' => 'CSC301-2024',
                    'schedule' => 'Monday 10:00 AM - 12:00 PM',
                    'description' => 'Introduction to Software Engineering',
                    'semester' => 'First Semester',
                    'academic_year' => '2024/2025',
                    'is_active' => true,
                ]
            );
        }
    }
}
