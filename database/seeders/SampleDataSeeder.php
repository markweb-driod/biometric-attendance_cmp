<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Lecturer;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove unwanted sample students
        \App\Models\Student::whereIn('name', [
            'John Doe', 'Jane Smith', 'Mike Johnson', 'Sarah Lee', 'David Wilson'
        ])->delete();

        // Assign only Computer Science students to each class with matching level
        $classes = \App\Models\Classroom::all();
        foreach ($classes as $class) {
            $students = \App\Models\Student::where('department', 'Computer Science')
                ->where('level', $class->level)
                ->pluck('id');
            $class->students()->sync($students);
        }

        // Ensure all classes are assigned to lecturers
        $lecturers = \App\Models\Lecturer::all();
        if ($lecturers->count() > 0) {
            $classes = \App\Models\Classroom::whereNull('lecturer_id')->get();
            foreach ($classes as $class) {
                $class->lecturer_id = $lecturers->first()->id;
                $class->save();
            }
        }

        // Debug: Log class assignments
        $classes = \App\Models\Classroom::with(['students', 'lecturer'])->get();
        foreach ($classes as $class) {
            \Log::info("Class: {$class->course_code} - {$class->class_name}");
            \Log::info("  Lecturer: " . ($class->lecturer ? $class->lecturer->name : 'None'));
            \Log::info("  Students: " . $class->students->count());
            \Log::info("  Level: {$class->level}");
        }

        // Get lecturers
        $lecturers = Lecturer::all();
        
        if ($lecturers->isEmpty()) {
            $this->command->error('No lecturers found. Please run LecturerSeeder first.');
            return;
        }

        // Create sample students with proper department and level
        $students = [
            [
                'matric_number' => '02200470364',
                'full_name' => 'Test Student',
                'email' => 'test.student@nsuk.edu.ng',
                'phone' => '08012345678',
                'department' => 'Computer Science',
                'level' => 100,
                'academic_level' => '100 Level',
            ],
            [
                'matric_number' => '2021/123456',
                'full_name' => 'John Doe',
                'email' => 'john.doe@nsuk.edu.ng',
                'phone' => '08012345678',
                'department' => 'Computer Science',
                'level' => 200,
                'academic_level' => '200 Level',
            ],
            [
                'matric_number' => '2021/123457',
                'full_name' => 'Jane Smith',
                'email' => 'jane.smith@nsuk.edu.ng',
                'phone' => '08012345679',
                'department' => 'Computer Science',
                'level' => 300,
                'academic_level' => '300 Level',
            ],
            [
                'matric_number' => '2021/123458',
                'full_name' => 'Mike Johnson',
                'email' => 'mike.johnson@nsuk.edu.ng',
                'phone' => '08012345680',
                'department' => 'Computer Science',
                'level' => 400,
                'academic_level' => '400 Level',
            ],
        ];

        foreach ($students as $studentData) {
            Student::updateOrCreate(
                ['matric_number' => $studentData['matric_number']],
                $studentData
            );
        }

        // Create sample classrooms with lecturer assignments and levels
        $classrooms = [
            [
                'class_name' => 'Introduction to Computer Science',
                'course_code' => 'CSC101',
                'pin' => '1234',
                'schedule' => 'Monday, Wednesday, Friday 9:00 AM - 10:30 AM',
                'description' => 'Basic concepts of computer science and programming',
                'lecturer_id' => $lecturers[0]->id, // Dr. John Doe
                'level' => 100,
            ],
            [
                'class_name' => 'Data Structures and Algorithms',
                'course_code' => 'CSC201',
                'pin' => '5678',
                'schedule' => 'Tuesday, Thursday 11:00 AM - 12:30 PM',
                'description' => 'Advanced data structures and algorithm analysis',
                'lecturer_id' => $lecturers[1]->id, // Prof. Jane Smith
                'level' => 200,
            ],
            [
                'class_name' => 'Web Development',
                'course_code' => 'CSC301',
                'pin' => '9012',
                'schedule' => 'Monday, Wednesday 2:00 PM - 3:30 PM',
                'description' => 'Modern web development technologies',
                'lecturer_id' => $lecturers[2]->id, // Dr. Michael Johnson
                'level' => 300,
            ],
        ];

        foreach ($classrooms as $classroomData) {
            Classroom::updateOrCreate(
                ['course_code' => $classroomData['course_code']],
                $classroomData
            );
        }

        // Enroll students in classes by matching level
        $students = Student::all();
        $classrooms = Classroom::all();

        foreach ($classrooms as $classroom) {
            // Get students of the same level as the class
            $matchingStudents = $students->where('level', $classroom->level);
            $classroom->students()->attach($matchingStudents->pluck('id'));
        }

        $this->command->info('Sample data created successfully!');
        $this->command->info('Test credentials:');
        $this->command->info('Student: 02200470364, Class PIN: 1234');
        $this->command->info('Student: 2021/123456, Class PIN: 1234');
        $this->command->info('Student: 2021/123457, Class PIN: 5678');
        $this->command->info('Student: 2021/123458, Class PIN: 9012');
        $this->command->info('');
        $this->command->info('Lecturer credentials:');
        $this->command->info('Staff ID: LEC001, Password: password123');
        $this->command->info('Staff ID: LEC002, Password: password123');
        $this->command->info('Staff ID: LEC003, Password: password123');
    }
}
