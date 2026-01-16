<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AttendanceCalculationService;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    private AttendanceCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AttendanceCalculationService();
    }

    public function test_calculates_student_attendance_correctly()
    {
        // Create test data
        $department = Department::factory()->create();
        $student = Student::factory()->create(['department_id' => $department->id]);
        $course = Course::factory()->create();
        $classroom = Classroom::factory()->create(['course_id' => $course->id]);
        
        // Create 10 attendance sessions
        $sessions = AttendanceSession::factory()->count(10)->create([
            'classroom_id' => $classroom->id
        ]);
        
        // Student attended 7 out of 10 sessions
        foreach ($sessions->take(7) as $session) {
            Attendance::factory()->create([
                'student_id' => $student->id,
                'attendance_session_id' => $session->id
            ]);
        }
        
        // Attach student to classroom
        $classroom->students()->attach($student->id);
        
        $result = $this->service->calculateStudentAttendance($student->id);
        
        $this->assertEquals(7, $result['total_present']);
        $this->assertEquals(10, $result['total_sessions']);
        $this->assertEquals(70.0, $result['percentage']);
        $this->assertEquals('good', $result['status']);
    }

    public function test_calculates_attendance_with_course_filter()
    {
        $department = Department::factory()->create();
        $student = Student::factory()->create(['department_id' => $department->id]);
        $course1 = Course::factory()->create();
        $course2 = Course::factory()->create();
        
        $classroom1 = Classroom::factory()->create(['course_id' => $course1->id]);
        $classroom2 = Classroom::factory()->create(['course_id' => $course2->id]);
        
        // Course 1: 5 sessions, attended 4
        $sessions1 = AttendanceSession::factory()->count(5)->create(['classroom_id' => $classroom1->id]);
        foreach ($sessions1->take(4) as $session) {
            Attendance::factory()->create([
                'student_id' => $student->id,
                'attendance_session_id' => $session->id
            ]);
        }
        
        // Course 2: 3 sessions, attended 1
        $sessions2 = AttendanceSession::factory()->count(3)->create(['classroom_id' => $classroom2->id]);
        Attendance::factory()->create([
            'student_id' => $student->id,
            'attendance_session_id' => $sessions2->first()->id
        ]);
        
        $classroom1->students()->attach($student->id);
        $classroom2->students()->attach($student->id);
        
        $result = $this->service->calculateStudentAttendance($student->id, $course1->id);
        
        $this->assertEquals(4, $result['total_present']);
        $this->assertEquals(5, $result['total_sessions']);
        $this->assertEquals(80.0, $result['percentage']);
        $this->assertEquals('excellent', $result['status']);
    }

    public function test_identifies_students_below_threshold()
    {
        $department = Department::factory()->create();
        
        // Create students with different attendance rates
        $students = Student::factory()->count(5)->create(['department_id' => $department->id]);
        $course = Course::factory()->create();
        $classroom = Classroom::factory()->create(['course_id' => $course->id]);
        
        $sessions = AttendanceSession::factory()->count(10)->create(['classroom_id' => $classroom->id]);
        
        // Student 1: 90% attendance (9/10)
        $this->createAttendanceForStudent($students[0], $sessions, 9, $classroom);
        
        // Student 2: 60% attendance (6/10) - below threshold
        $this->createAttendanceForStudent($students[1], $sessions, 6, $classroom);
        
        // Student 3: 80% attendance (8/10)
        $this->createAttendanceForStudent($students[2], $sessions, 8, $classroom);
        
        // Student 4: 40% attendance (4/10) - below threshold
        $this->createAttendanceForStudent($students[3], $sessions, 4, $classroom);
        
        // Student 5: 75% attendance (7.5/10 = 7/10) - at threshold
        $this->createAttendanceForStudent($students[4], $sessions, 7, $classroom);
        
        $belowThreshold = $this->service->getStudentsBelowThreshold($department->id, 75.0);
        
        $this->assertCount(2, $belowThreshold);
        
        // Should be sorted by percentage (lowest first)
        $attendanceData = $belowThreshold->pluck('attendance_data.percentage')->toArray();
        $this->assertEquals([40.0, 60.0], $attendanceData);
    }

    public function test_get_attendance_status_returns_correct_status()
    {
        $this->assertEquals('excellent', $this->service->getAttendanceStatus(85.0));
        $this->assertEquals('excellent', $this->service->getAttendanceStatus(75.0));
        $this->assertEquals('good', $this->service->getAttendanceStatus(65.0));
        $this->assertEquals('good', $this->service->getAttendanceStatus(60.0));
        $this->assertEquals('warning', $this->service->getAttendanceStatus(55.0));
        $this->assertEquals('warning', $this->service->getAttendanceStatus(50.0));
        $this->assertEquals('critical', $this->service->getAttendanceStatus(45.0));
        $this->assertEquals('critical', $this->service->getAttendanceStatus(0.0));
    }

    public function test_calculates_department_attendance_summary()
    {
        $department = Department::factory()->create();
        $students = Student::factory()->count(4)->create(['department_id' => $department->id]);
        $course = Course::factory()->create();
        $classroom = Classroom::factory()->create(['course_id' => $course->id]);
        
        $sessions = AttendanceSession::factory()->count(10)->create(['classroom_id' => $classroom->id]);
        
        // Create different attendance patterns
        $this->createAttendanceForStudent($students[0], $sessions, 9, $classroom); // 90% - excellent
        $this->createAttendanceForStudent($students[1], $sessions, 6, $classroom); // 60% - good
        $this->createAttendanceForStudent($students[2], $sessions, 5, $classroom); // 50% - warning
        $this->createAttendanceForStudent($students[3], $sessions, 3, $classroom); // 30% - critical
        
        $summary = $this->service->getDepartmentAttendanceSummary($department->id);
        
        $this->assertEquals(4, $summary['total_students']);
        $this->assertEquals(57.5, $summary['average_attendance']); // (90+60+50+30)/4
        $this->assertEquals(1, $summary['excellent_count']);
        $this->assertEquals(1, $summary['good_count']);
        $this->assertEquals(1, $summary['warning_count']);
        $this->assertEquals(1, $summary['critical_count']);
    }

    public function test_handles_empty_department()
    {
        $department = Department::factory()->create();
        
        $summary = $this->service->getDepartmentAttendanceSummary($department->id);
        
        $this->assertEquals(0, $summary['total_students']);
        $this->assertEquals(0, $summary['average_attendance']);
        $this->assertEquals(0, $summary['excellent_count']);
        $this->assertEquals(0, $summary['good_count']);
        $this->assertEquals(0, $summary['warning_count']);
        $this->assertEquals(0, $summary['critical_count']);
    }

    private function createAttendanceForStudent($student, $sessions, $attendedCount, $classroom)
    {
        $classroom->students()->attach($student->id);
        
        foreach ($sessions->take($attendedCount) as $session) {
            Attendance::factory()->create([
                'student_id' => $student->id,
                'attendance_session_id' => $session->id
            ]);
        }
    }
}