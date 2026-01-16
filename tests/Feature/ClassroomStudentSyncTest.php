<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\ClassroomController;
use App\Models\AcademicLevel;
use App\Models\Course;
use App\Models\Department;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ClassroomStudentSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_classroom_creation_syncs_students_from_all_course_departments(): void
    {
        $level = AcademicLevel::create([
            'name' => '200 Level',
            'code' => '200',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        $deptA = Department::factory()->create();
        $deptB = Department::factory()->create();

        $lecturerUser = User::factory()->create();
        $lecturer = Lecturer::create([
            'user_id' => $lecturerUser->id,
            'department_id' => $deptA->id,
            'staff_id' => 'LEC' . rand(1000, 9999),
            'title' => 'Ms.',
            'is_active' => true,
        ]);

        // Create students in both departments at the course level (without factories)
        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create();
            Student::create([
                'user_id' => $user->id,
                'matric_number' => 'A' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'phone' => '000',
                'department_id' => $deptA->id,
                'academic_level_id' => $level->id,
                'reference_image_path' => null,
                'face_registration_enabled' => false,
                'is_active' => true,
            ]);
        }
        for ($i = 0; $i < 7; $i++) {
            $user = User::factory()->create();
            Student::create([
                'user_id' => $user->id,
                'matric_number' => 'B' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'phone' => '000',
                'department_id' => $deptB->id,
                'academic_level_id' => $level->id,
                'reference_image_path' => null,
                'face_registration_enabled' => false,
                'is_active' => true,
            ]);
        }

        $course = Course::create([
            'course_code' => 'CSC102',
            'course_name' => 'Data Structures',
            'credit_units' => 3,
            'academic_level_id' => $level->id,
            'is_active' => true,
        ]);
        $course->departments()->sync([$deptA->id, $deptB->id]);

        // Assign course to lecturer (required by controller)
        \DB::table('lecturer_course')->insert([
            'lecturer_id' => $lecturer->id,
            'course_id' => $course->id,
            'is_active' => true,
            'assigned_at' => now(),
            'unassigned_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Build request for classroom creation
        $request = Request::create('/api/classrooms', 'POST', [
            'class_name' => 'CSC102 A',
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'pin' => 'CS102A',
            'schedule' => 'Mon 9-11',
            'description' => 'Auto-created for testing',
            'is_active' => true,
        ]);

        // Controller call
        $controller = new ClassroomController();
        $response = $controller->store($request);

        $data = $response->getData(true);
        $this->assertTrue($data['success']);

        $classroomId = $data['data']['id'];
        $classroom = \App\Models\Classroom::with('students')->find($classroomId);

        // Expect 12 students (5 from A + 7 from B) synced
        $this->assertEquals(12, $classroom->students->count());
    }
}


