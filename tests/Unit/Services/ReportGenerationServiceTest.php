<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ReportGenerationService;
use App\Services\AttendanceCalculationService;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\AcademicLevel;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Mockery;

class ReportGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReportGenerationService $service;
    private $mockAttendanceService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockAttendanceService = Mockery::mock(AttendanceCalculationService::class);
        $this->service = new ReportGenerationService($this->mockAttendanceService);
        
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_generates_attendance_report()
    {
        $department = Department::factory()->create(['name' => 'Computer Science']);
        $level = AcademicLevel::factory()->create(['name' => '200 Level']);
        
        $students = Student::factory()->count(3)->create([
            'department_id' => $department->id,
            'academic_level_id' => $level->id,
        ]);

        // Mock attendance calculations
        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($students[0]->id, null)
            ->andReturn([
                'total_sessions' => 10,
                'total_present' => 9,
                'percentage' => 90.0,
                'status' => 'excellent'
            ]);

        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($students[1]->id, null)
            ->andReturn([
                'total_sessions' => 10,
                'total_present' => 6,
                'percentage' => 60.0,
                'status' => 'good'
            ]);

        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($students[2]->id, null)
            ->andReturn([
                'total_sessions' => 10,
                'total_present' => 4,
                'percentage' => 40.0,
                'status' => 'critical'
            ]);

        $report = $this->service->generateAttendanceReport($department->id);

        $this->assertEquals($department->id, $report['department']['id']);
        $this->assertEquals('Computer Science', $report['department']['name']);
        $this->assertEquals(3, $report['summary']['total_students']);
        $this->assertEquals(63.33, $report['summary']['average_attendance']); // (90+60+40)/3
        
        $this->assertEquals(1, $report['summary']['distribution']['excellent']);
        $this->assertEquals(1, $report['summary']['distribution']['good']);
        $this->assertEquals(0, $report['summary']['distribution']['warning']);
        $this->assertEquals(1, $report['summary']['distribution']['critical']);

        $this->assertCount(3, $report['students']);
        
        // Should be sorted by attendance percentage (highest first)
        $this->assertEquals(90.0, $report['students'][0]['attendance_percentage']);
        $this->assertEquals(60.0, $report['students'][1]['attendance_percentage']);
        $this->assertEquals(40.0, $report['students'][2]['attendance_percentage']);
    }

    public function test_generates_attendance_report_with_filters()
    {
        $department = Department::factory()->create();
        $course = Course::factory()->create();
        $student = Student::factory()->create(['department_id' => $department->id]);
        $classroom = Classroom::factory()->create(['course_id' => $course->id]);
        
        // Attach student to classroom
        $classroom->students()->attach($student->id);

        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($student->id, $course->id)
            ->andReturn([
                'total_sessions' => 5,
                'total_present' => 4,
                'percentage' => 80.0,
                'status' => 'excellent'
            ]);

        $filters = ['course_id' => $course->id];
        $report = $this->service->generateAttendanceReport($department->id, $filters);

        $this->assertEquals($filters, $report['filters']);
        $this->assertCount(1, $report['students']);
        $this->assertEquals(80.0, $report['students'][0]['attendance_percentage']);
    }

    public function test_generates_performance_report()
    {
        $department = Department::factory()->create(['name' => 'Mathematics']);
        $lecturer1 = Lecturer::factory()->create([
            'department_id' => $department->id,
            'staff_id' => 'STAFF001'
        ]);
        $lecturer2 = Lecturer::factory()->create([
            'department_id' => $department->id,
            'staff_id' => 'STAFF002'
        ]);

        $user1 = User::factory()->create(['name' => 'Dr. Smith']);
        $user2 = User::factory()->create(['name' => 'Prof. Johnson']);
        
        $lecturer1->user()->associate($user1);
        $lecturer2->user()->associate($user2);
        $lecturer1->save();
        $lecturer2->save();

        $classroom1 = Classroom::factory()->create(['lecturer_id' => $lecturer1->id]);
        $classroom2 = Classroom::factory()->create(['lecturer_id' => $lecturer2->id]);

        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Create sessions for lecturer 1
        AttendanceSession::factory()->count(2)->create([
            'classroom_id' => $classroom1->id,
            'created_at' => $startDate->copy()->addDays(5),
            'end_time' => $startDate->copy()->addDays(5)->addMinutes(60),
            'is_punctual' => true,
            'is_out_of_bounds' => false,
        ]);

        // Create sessions for lecturer 2
        AttendanceSession::factory()->create([
            'classroom_id' => $classroom2->id,
            'created_at' => $startDate->copy()->addDays(10),
            'end_time' => $startDate->copy()->addDays(10)->addMinutes(45),
            'is_punctual' => false,
            'is_out_of_bounds' => true,
        ]);

        $report = $this->service->generatePerformanceReport($department->id, $startDate, $endDate);

        $this->assertEquals($department->id, $report['department']['id']);
        $this->assertEquals('Mathematics', $report['department']['name']);
        $this->assertEquals($startDate->toDateString(), $report['period']['start_date']);
        $this->assertEquals($endDate->toDateString(), $report['period']['end_date']);

        $this->assertEquals(2, $report['summary']['total_lecturers']);
        $this->assertEquals(3, $report['summary']['total_sessions']);

        $this->assertCount(2, $report['lecturers']);
        
        // Should be sorted by total sessions (most active first)
        $this->assertEquals('STAFF001', $report['lecturers'][0]['staff_id']);
        $this->assertEquals(2, $report['lecturers'][0]['total_sessions']);
        $this->assertEquals(100.0, $report['lecturers'][0]['punctuality_rate']);
        $this->assertEquals(100.0, $report['lecturers'][0]['geofence_compliance']);
        
        $this->assertEquals('STAFF002', $report['lecturers'][1]['staff_id']);
        $this->assertEquals(1, $report['lecturers'][1]['total_sessions']);
        $this->assertEquals(0.0, $report['lecturers'][1]['punctuality_rate']);
        $this->assertEquals(0.0, $report['lecturers'][1]['geofence_compliance']);
    }

    public function test_generates_compliance_report()
    {
        $department = Department::factory()->create(['name' => 'Physics']);
        $level = AcademicLevel::factory()->create(['name' => '300 Level']);
        
        $students = Student::factory()->count(4)->create([
            'department_id' => $department->id,
            'academic_level_id' => $level->id,
        ]);

        // Mock attendance calculations
        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($students[0]->id)
            ->andReturn(['total_sessions' => 10, 'total_present' => 8, 'percentage' => 80.0]);

        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($students[1]->id)
            ->andReturn(['total_sessions' => 10, 'total_present' => 7, 'percentage' => 70.0]);

        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($students[2]->id)
            ->andReturn(['total_sessions' => 10, 'total_present' => 6, 'percentage' => 60.0]);

        $this->mockAttendanceService->shouldReceive('calculateStudentAttendance')
            ->with($students[3]->id)
            ->andReturn(['total_sessions' => 10, 'total_present' => 9, 'percentage' => 90.0]);

        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        $threshold = 75.0;

        $report = $this->service->generateComplianceReport($department->id, $startDate, $endDate, $threshold);

        $this->assertEquals(4, $report['summary']['total_students']);
        $this->assertEquals(2, $report['summary']['compliant_students']); // 80% and 90%
        $this->assertEquals(2, $report['summary']['non_compliant_students']); // 70% and 60%
        $this->assertEquals(50.0, $report['summary']['compliance_rate']); // 2/4 * 100
        $this->assertEquals(75.0, $report['summary']['threshold']);

        $this->assertCount(4, $report['students']);
        
        // Should be sorted by compliance status (non-compliant first) then by percentage
        $this->assertFalse($report['students'][0]['is_compliant']);
        $this->assertEquals(70.0, $report['students'][0]['attendance_percentage']);
        
        $this->assertFalse($report['students'][1]['is_compliant']);
        $this->assertEquals(60.0, $report['students'][1]['attendance_percentage']);
        
        $this->assertTrue($report['students'][2]['is_compliant']);
        $this->assertEquals(90.0, $report['students'][2]['attendance_percentage']);
        
        $this->assertTrue($report['students'][3]['is_compliant']);
        $this->assertEquals(80.0, $report['students'][3]['attendance_percentage']);
    }

    public function test_exports_to_csv()
    {
        $reportData = [
            'department' => ['id' => 1, 'name' => 'Test Department'],
            'students' => [
                [
                    'matric_number' => 'CS/2021/001',
                    'full_name' => 'John Doe',
                    'level' => '200 Level',
                    'total_sessions' => 10,
                    'attended_sessions' => 8,
                    'attendance_percentage' => 80.0,
                    'status_label' => 'Excellent (≥75%)',
                ],
                [
                    'matric_number' => 'CS/2021/002',
                    'full_name' => 'Jane Smith',
                    'level' => '200 Level',
                    'total_sessions' => 10,
                    'attended_sessions' => 6,
                    'attendance_percentage' => 60.0,
                    'status_label' => 'Good (60-74%)',
                ],
            ],
        ];

        $filePath = $this->service->exportToCSV($reportData, 'attendance');

        $this->assertStringStartsWith('reports/csv/attendance_report_', $filePath);
        $this->assertStringEndsWith('.csv', $filePath);
        
        Storage::disk('public')->assertExists($filePath);
        
        $csvContent = Storage::disk('public')->get($filePath);
        $this->assertStringContains('Matric Number,Full Name,Level', $csvContent);
        $this->assertStringContains('CS/2021/001,John Doe,200 Level', $csvContent);
        $this->assertStringContains('CS/2021/002,Jane Smith,200 Level', $csvContent);
    }

    public function test_exports_performance_report_to_csv()
    {
        $reportData = [
            'department' => ['id' => 1, 'name' => 'Test Department'],
            'lecturers' => [
                [
                    'staff_id' => 'STAFF001',
                    'name' => 'Dr. Smith',
                    'total_sessions' => 15,
                    'completion_rate' => 95.0,
                    'punctuality_rate' => 90.0,
                    'geofence_compliance' => 100.0,
                    'average_duration_minutes' => 55.5,
                ],
            ],
        ];

        $filePath = $this->service->exportToCSV($reportData, 'performance');

        Storage::disk('public')->assertExists($filePath);
        
        $csvContent = Storage::disk('public')->get($filePath);
        $this->assertStringContains('Staff ID,Name,Total Sessions', $csvContent);
        $this->assertStringContains('STAFF001,Dr. Smith,15', $csvContent);
    }

    public function test_exports_compliance_report_to_csv()
    {
        $reportData = [
            'department' => ['id' => 1, 'name' => 'Test Department'],
            'students' => [
                [
                    'matric_number' => 'CS/2021/001',
                    'full_name' => 'John Doe',
                    'level' => '200 Level',
                    'attendance_percentage' => 80.0,
                    'compliance_status' => 'Compliant',
                    'sessions_attended' => 8,
                    'total_sessions' => 10,
                ],
            ],
        ];

        $filePath = $this->service->exportToCSV($reportData, 'compliance');

        Storage::disk('public')->assertExists($filePath);
        
        $csvContent = Storage::disk('public')->get($filePath);
        $this->assertStringContains('Matric Number,Full Name,Level,Attendance %', $csvContent);
        $this->assertStringContains('CS/2021/001,John Doe,200 Level,80', $csvContent);
    }

    public function test_calculates_working_days_correctly()
    {
        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('calculateWorkingDays');
        $method->setAccessible(true);

        // Test a week with 5 working days (Monday to Friday)
        $startDate = Carbon::create(2024, 1, 1); // Monday
        $endDate = Carbon::create(2024, 1, 5);   // Friday
        
        $workingDays = $method->invoke($this->service, $startDate, $endDate);
        $this->assertEquals(5, $workingDays);

        // Test a week including weekend
        $startDate = Carbon::create(2024, 1, 1); // Monday
        $endDate = Carbon::create(2024, 1, 7);   // Sunday
        
        $workingDays = $method->invoke($this->service, $startDate, $endDate);
        $this->assertEquals(5, $workingDays); // Still 5 working days
    }

    public function test_generates_filename_correctly()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('generateFilename');
        $method->setAccessible(true);

        $filename = $method->invoke($this->service, 'attendance', 'pdf');
        
        $this->assertStringStartsWith('attendance_report_', $filename);
        $this->assertStringEndsWith('.pdf', $filename);
        $this->assertMatchesRegularExpression('/attendance_report_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.pdf/', $filename);
    }

    public function test_gets_status_label_correctly()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getStatusLabel');
        $method->setAccessible(true);

        $this->assertEquals('Excellent (≥75%)', $method->invoke($this->service, 'excellent'));
        $this->assertEquals('Good (60-74%)', $method->invoke($this->service, 'good'));
        $this->assertEquals('Warning (50-59%)', $method->invoke($this->service, 'warning'));
        $this->assertEquals('Critical (<50%)', $method->invoke($this->service, 'critical'));
        $this->assertEquals('Unknown', $method->invoke($this->service, 'invalid'));
    }

    public function test_calculates_teaching_frequency()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('calculateTeachingFrequency');
        $method->setAccessible(true);

        $startDate = Carbon::create(2024, 1, 1); // Monday
        $endDate = Carbon::create(2024, 1, 5);   // Friday (5 working days)
        
        $frequency = $method->invoke($this->service, 10, $startDate, $endDate);
        $this->assertEquals(2.0, $frequency); // 10 sessions / 5 working days
    }
}