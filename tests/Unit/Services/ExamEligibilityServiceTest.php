<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ExamEligibilityService;
use App\Services\AttendanceCalculationService;
use App\Models\Student;
use App\Models\ExamEligibility;
use App\Models\Department;
use App\Models\HOD;
use App\Models\AuditLog;
use App\Events\ExamEligibilityUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Mockery;

class ExamEligibilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExamEligibilityService $service;
    private $mockAttendanceService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockAttendanceService = Mockery::mock(AttendanceCalculationService::class);
        $this->service = new ExamEligibilityService($this->mockAttendanceService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_validates_eligibility_for_department()
    {
        Event::fake();
        
        $department = Department::factory()->create();
        $students = Student::factory()->count(3)->create(['department_id' => $department->id]);

        // Mock attendance calculations
        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($students[0]->id)
            ->andReturn(['percentage' => 80.0]);
            
        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($students[1]->id)
            ->andReturn(['percentage' => 60.0]);
            
        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($students[2]->id)
            ->andReturn(['percentage' => 90.0]);

        $result = $this->service->validateEligibility($department->id, 75.0);

        $this->assertEquals(2, $result['eligible']); // 80% and 90%
        $this->assertEquals(1, $result['ineligible']); // 60%
        $this->assertCount(3, $result['updated']);
        $this->assertEmpty($result['errors']);

        // Check database records
        $this->assertDatabaseHas('exam_eligibilities', [
            'student_id' => $students[0]->id,
            'attendance_percentage' => 80.0,
            'is_eligible' => true,
        ]);

        $this->assertDatabaseHas('exam_eligibilities', [
            'student_id' => $students[1]->id,
            'attendance_percentage' => 60.0,
            'is_eligible' => false,
        ]);

        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'validate_exam_eligibility',
            'status' => 'success',
        ]);

        // Check event was fired
        Event::assertDispatched(ExamEligibilityUpdated::class);
    }

    public function test_overrides_eligibility_with_audit_trail()
    {
        $department = Department::factory()->create();
        $student = Student::factory()->create(['department_id' => $department->id]);
        $hod = HOD::factory()->create(['department_id' => $department->id]);

        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($student->id)
            ->andReturn(['percentage' => 60.0]);

        $eligibility = $this->service->overrideEligibility(
            $student->id,
            true,
            'Medical exemption due to hospitalization',
            $hod->id
        );

        $this->assertTrue($eligibility->is_eligible);
        $this->assertEquals($hod->id, $eligibility->override_by);
        $this->assertEquals('Medical exemption due to hospitalization', $eligibility->override_reason);
        $this->assertEquals(60.0, $eligibility->attendance_percentage);

        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $hod->id,
            'action' => 'override_exam_eligibility',
            'status' => 'success',
        ]);
    }

    public function test_gets_current_semester_correctly()
    {
        // Test September (First semester)
        Carbon::setTestNow(Carbon::create(2024, 9, 15));
        $this->assertEquals('First', $this->service->getCurrentSemester());

        // Test December (First semester)
        Carbon::setTestNow(Carbon::create(2024, 12, 15));
        $this->assertEquals('First', $this->service->getCurrentSemester());

        // Test January (First semester)
        Carbon::setTestNow(Carbon::create(2024, 1, 15));
        $this->assertEquals('First', $this->service->getCurrentSemester());

        // Test March (Second semester)
        Carbon::setTestNow(Carbon::create(2024, 3, 15));
        $this->assertEquals('Second', $this->service->getCurrentSemester());

        // Test June (Second semester)
        Carbon::setTestNow(Carbon::create(2024, 6, 15));
        $this->assertEquals('Second', $this->service->getCurrentSemester());

        // Test July (Summer)
        Carbon::setTestNow(Carbon::create(2024, 7, 15));
        $this->assertEquals('Summer', $this->service->getCurrentSemester());

        // Test August (Summer)
        Carbon::setTestNow(Carbon::create(2024, 8, 15));
        $this->assertEquals('Summer', $this->service->getCurrentSemester());
    }

    public function test_gets_current_academic_year_correctly()
    {
        // Test September 2024 (start of 2024/2025 academic year)
        Carbon::setTestNow(Carbon::create(2024, 9, 15));
        $this->assertEquals('2024/2025', $this->service->getCurrentAcademicYear());

        // Test December 2024 (still 2024/2025)
        Carbon::setTestNow(Carbon::create(2024, 12, 15));
        $this->assertEquals('2024/2025', $this->service->getCurrentAcademicYear());

        // Test March 2025 (still 2024/2025)
        Carbon::setTestNow(Carbon::create(2025, 3, 15));
        $this->assertEquals('2024/2025', $this->service->getCurrentAcademicYear());

        // Test August 2025 (still 2024/2025)
        Carbon::setTestNow(Carbon::create(2025, 8, 15));
        $this->assertEquals('2024/2025', $this->service->getCurrentAcademicYear());
    }

    public function test_gets_eligibility_status_for_department()
    {
        $department = Department::factory()->create();
        $students = Student::factory()->count(3)->create(['department_id' => $department->id]);
        $hod = HOD::factory()->create(['department_id' => $department->id]);

        // Create eligibility records
        ExamEligibility::factory()->create([
            'student_id' => $students[0]->id,
            'attendance_percentage' => 85.0,
            'is_eligible' => true,
            'semester' => 'First',
            'academic_year' => '2024/2025',
        ]);

        ExamEligibility::factory()->create([
            'student_id' => $students[1]->id,
            'attendance_percentage' => 60.0,
            'is_eligible' => false,
            'semester' => 'First',
            'academic_year' => '2024/2025',
        ]);

        ExamEligibility::factory()->create([
            'student_id' => $students[2]->id,
            'attendance_percentage' => 70.0,
            'is_eligible' => true,
            'override_by' => $hod->id,
            'override_reason' => 'Medical exemption',
            'semester' => 'First',
            'academic_year' => '2024/2025',
        ]);

        $result = $this->service->getEligibilityStatus($department->id, 'First', '2024/2025');

        $this->assertCount(3, $result);
        
        // Should be ordered by eligibility (eligible first) then by attendance percentage
        $this->assertTrue($result[0]->is_eligible);
        $this->assertEquals(85.0, $result[0]->attendance_percentage);
        
        $this->assertTrue($result[1]->is_eligible);
        $this->assertEquals(70.0, $result[1]->attendance_percentage);
        $this->assertNotNull($result[1]->override_by);
        
        $this->assertFalse($result[2]->is_eligible);
        $this->assertEquals(60.0, $result[2]->attendance_percentage);
    }

    public function test_generates_clearance_list()
    {
        $department = Department::factory()->create();
        $students = Student::factory()->count(2)->create(['department_id' => $department->id]);

        // Create eligibility records - only eligible students should be included
        ExamEligibility::factory()->create([
            'student_id' => $students[0]->id,
            'attendance_percentage' => 85.0,
            'is_eligible' => true,
            'semester' => 'First',
            'academic_year' => '2024/2025',
        ]);

        ExamEligibility::factory()->create([
            'student_id' => $students[1]->id,
            'attendance_percentage' => 60.0,
            'is_eligible' => false,
            'semester' => 'First',
            'academic_year' => '2024/2025',
        ]);

        $clearanceList = $this->service->generateClearanceList($department->id, 'First', '2024/2025');

        $this->assertEquals($department->id, $clearanceList['department_id']);
        $this->assertEquals('First', $clearanceList['semester']);
        $this->assertEquals('2024/2025', $clearanceList['academic_year']);
        $this->assertEquals(1, $clearanceList['total_eligible']);
        $this->assertCount(1, $clearanceList['students']);
        
        $student = $clearanceList['students'][0];
        $this->assertEquals($students[0]->matric_number, $student['matric_number']);
        $this->assertEquals(85.0, $student['attendance_percentage']);
        $this->assertFalse($student['is_override']);
    }

    public function test_gets_eligibility_statistics()
    {
        $department = Department::factory()->create();
        $students = Student::factory()->count(4)->create(['department_id' => $department->id]);
        $hod = HOD::factory()->create(['department_id' => $department->id]);

        // Create eligibility records with different statuses
        ExamEligibility::factory()->create([
            'student_id' => $students[0]->id,
            'attendance_percentage' => 85.0,
            'is_eligible' => true,
            'semester' => 'First',
            'academic_year' => '2024/2025',
        ]);

        ExamEligibility::factory()->create([
            'student_id' => $students[1]->id,
            'attendance_percentage' => 90.0,
            'is_eligible' => true,
            'semester' => 'First',
            'academic_year' => '2024/2025',
        ]);

        ExamEligibility::factory()->create([
            'student_id' => $students[2]->id,
            'attendance_percentage' => 60.0,
            'is_eligible' => false,
            'semester' => 'First',
            'academic_year' => '2024/2025',
        ]);

        ExamEligibility::factory()->create([
            'student_id' => $students[3]->id,
            'attendance_percentage' => 70.0,
            'is_eligible' => true,
            'override_by' => $hod->id,
            'semester' => 'First',
            'academic_year' => '2024/2025',
        ]);

        $stats = $this->service->getEligibilityStatistics($department->id, 'First', '2024/2025');

        $this->assertEquals(4, $stats['total_students']);
        $this->assertEquals(3, $stats['eligible_count']);
        $this->assertEquals(1, $stats['ineligible_count']);
        $this->assertEquals(1, $stats['override_count']);
        $this->assertEquals(75.0, $stats['eligibility_rate']); // 3/4 * 100
        $this->assertEquals(76.25, $stats['average_attendance']); // (85+90+60+70)/4
    }

    public function test_checks_if_validation_is_needed()
    {
        $department = Department::factory()->create();
        $students = Student::factory()->count(3)->create(['department_id' => $department->id]);

        // No eligibility records exist yet
        $this->assertTrue($this->service->isValidationNeeded($department->id, 'First', '2024/2025'));

        // Create eligibility for only 2 out of 3 students
        ExamEligibility::factory()->count(2)->create([
            'semester' => 'First',
            'academic_year' => '2024/2025',
        ]);

        // Still need validation for the remaining student
        $this->assertTrue($this->service->isValidationNeeded($department->id, 'First', '2024/2025'));

        // Create eligibility for the third student
        ExamEligibility::factory()->create([
            'student_id' => $students[2]->id,
            'semester' => 'First',
            'academic_year' => '2024/2025',
        ]);

        // Now all students have eligibility records
        $this->assertFalse($this->service->isValidationNeeded($department->id, 'First', '2024/2025'));
    }

    public function test_handles_validation_errors_gracefully()
    {
        Log::fake();
        
        $department = Department::factory()->create();
        $student = Student::factory()->create(['department_id' => $department->id]);

        // Mock attendance service to throw exception
        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($student->id)
            ->andThrow(new \Exception('Database connection failed'));

        $result = $this->service->validateEligibility($department->id, 75.0);

        $this->assertEquals(0, $result['eligible']);
        $this->assertEquals(0, $result['ineligible']);
        $this->assertEmpty($result['updated']);
        $this->assertCount(1, $result['errors']);
        
        $error = $result['errors'][0];
        $this->assertEquals($student->id, $error['student_id']);
        $this->assertEquals($student->matric_number, $error['matric_number']);
        $this->assertStringContains('Database connection failed', $error['error']);
    }
}