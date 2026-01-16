<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Department;
use App\Models\AcademicLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseDepartmentRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_can_belong_to_multiple_departments(): void
    {
        $level = AcademicLevel::create([
            'name' => '100 Level',
            'code' => '100',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $deptA = Department::factory()->create();
        $deptB = Department::factory()->create();

        $course = Course::create([
            'course_code' => 'CSC101',
            'course_name' => 'Intro to Computing',
            'credit_units' => 3,
            'academic_level_id' => $level->id,
            'is_active' => true,
        ]);

        $course->departments()->sync([$deptA->id, $deptB->id]);

        $this->assertCount(2, $course->fresh()->departments);
        $this->assertTrue($deptA->courses()->where('courses.id', $course->id)->exists());
        $this->assertTrue($deptB->courses()->where('courses.id', $course->id)->exists());
    }
}


