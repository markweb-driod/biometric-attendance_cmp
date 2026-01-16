<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\HODDashboardService;
use App\Services\AttendanceCalculationService;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Classroom;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Department;
use App\Models\AcademicLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Mockery;

class HODDashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private HODDashboardService $service;
    private $mockAttendanceService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockAttendanceService = Mockery::mock(AttendanceCalculationService::class);
        $this->service = new HODDashboardService($this->mockAttendanceService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_gets_department_overview()
    {
        $department = Department::factory()->create();
        
        // Create test data
        $students = Student::factory()->count(50)->create(['department_id' => $department->id]);
        $lecturers = Lecturer::factory()->count(10)->create(['department_id' => $department->id]);
        $classrooms = Classroom::factory()->count(15)->create(['lecturer_id' => $lecturers->first()->id]);

        // Mock attendance service
        $this->mockAttendanceService->shouldReceive('getDepartmentAttendanceSummary')
            ->with($department->id)
            ->andReturn([
                'total_students' => 50,
                'average_attendance' => 75.5,
                'excellent_count' => 20,
                'good_count' => 15,
                'warning_count' => 10,
                'critical_count' => 5,
            ]);

        $overview = $this->service->getDepartmentOverview($department->id);

        $this->assertEquals(50, $overview['total_students']);
        $this->assertEquals(10, $overview['total_lecturers']);
        $this->assertEquals(15, $overview['total_classes']);
        $this->assertEquals(75.5, $overview['average_attendance']);
        $this->assertEquals(0, $overview['active_sessions']); // No active sessions created
        
        $this->assertArrayHasKey('attendance_distribution', $overview);
        $this->assertEquals(20, $overview['attendance_distribution']['excellent']);
        $this->assertEquals(15, $overview['attendance_distribution']['good']);
        $this->assertEquals(10, $overview['attendance_distribution']['warning']);
        $this->assertEquals(5, $overview['attendance_distribution']['critical']);
    }

    public function test_gets_active_sessions()
    {
        $department = Department::factory()->create();
        $lecturer = Lecturer::factory()->create(['department_id' => $department->id]);
        $user = User::factory()->create();
        $lecturer->user()->associate($user);
        $lecturer->save();
        
        $course = Course::factory()->create();
        $classroom = Classroom::factory()->create([
            'lecturer_id' => $lecturer->id,
            'course_id' => $course->id,
        ]);

        // Create active session (no end_time)
        $activeSession = AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'end_time' => null,
            'lecturer_latitude' => 9.0820,
            'lecturer_longitude' => 8.6753,
            'created_at' => now()->subMinutes(30),
        ]);

        // Create completed session (should not be included)
        AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'end_time' => now()->subMinutes(10),
            'created_at' => now()->subHours(2),
        ]);

        $activeSessions = $this->service->getActiveSessions($department->id);

        $this->assertCount(1, $activeSessions);
        
        $session = $activeSessions->first();
        $this->assertEquals($activeSession->id, $session['id']);
        $this->assertEquals($course->name, $session['course_name']);
        $this->assertEquals($course->code, $session['course_code']);
        $this->assertEquals($user->name, $session['lecturer_name']);
        $this->assertEquals(30, $session['duration_minutes']);
        $this->assertEquals(9.0820, $session['location']['latitude']);
        $this->assertEquals(8.6753, $session['location']['longitude']);
        $this->assertFalse($session['is_out_of_bounds']);
    }

    public function test_gets_threshold_compliance_data()
    {
        $department = Department::factory()->create();
        $students = Student::factory()->count(100)->create(['department_id' => $department->id]);
        
        // Mock 20 students below threshold
        $belowThresholdStudents = $students->take(20)->map(function ($student) {
            $student->attendance_data = [
                'percentage' => 60.0,
                'status' => 'warning'
            ];
            return $student;
        });

        $this->mockAttendanceService->shouldReceive('getStudentsBelowThreshold')
            ->with($department->id, 75.0)
            ->andReturn($belowThresholdStudents);

        $complianceData = $this->service->getThresholdComplianceData($department->id, 75.0);

        $this->assertEquals(100, $complianceData['total_students']);
        $this->assertEquals(80, $complianceData['compliant_students']);
        $this->assertEquals(20, $complianceData['non_compliant_students']);
        $this->assertEquals(80.0, $complianceData['compliance_rate']);
        $this->assertEquals(75.0, $complianceData['threshold']);
        $this->assertCount(10, $complianceData['students_below_threshold']); // Limited to 10
    }

    public function test_gets_attendance_chart_data_by_level()
    {
        $department = Department::factory()->create();
        
        // Create academic levels
        $level1 = AcademicLevel::factory()->create(['name' => '100 Level']);
        $level2 = AcademicLevel::factory()->create(['name' => '200 Level']);
        
        // Create students for each level
        $students1 = Student::factory()->count(30)->create([
            'department_id' => $department->id,
            'academic_level_id' => $level1->id,
        ]);
        
        $students2 = Student::factory()->count(25)->create([
            'department_id' => $department->id,
            'academic_level_id' => $level2->id,
        ]);

        // Mock attendance calculations
        $attendanceData1 = $students1->mapWithKeys(function ($student) {
            return [$student->id => ['percentage' => 80.0]];
        })->toArray();
        
        $attendanceData2 = $students2->mapWithKeys(function ($student) {
            return [$student->id => ['percentage' => 70.0]];
        })->toArray();

        $this->mockAttendanceService->shouldReceive('calculateBulkAttendance')
            ->with(Mockery::on(function ($students) use ($students1) {
                return $students->count() === $students1->count();
            }))
            ->andReturn($attendanceData1);
            
        $this->mockAttendanceService->shouldReceive('calculateBulkAttendance')
            ->with(Mockery::on(function ($students) use ($students2) {
                return $students->count() === $students2->count();
            }))
            ->andReturn($attendanceData2);

        $chartData = $this->service->getAttendanceChartData($department->id, 'level');

        $this->assertEquals('bar', $chartData['type']);
        $this->assertEquals('Attendance by Academic Level', $chartData['title']);
        $this->assertCount(2, $chartData['data']);
        
        $level1Data = collect($chartData['data'])->firstWhere('label', '100 Level');
        $this->assertEquals(80.0, $level1Data['value']);
        $this->assertEquals(30, $level1Data['student_count']);
        
        $level2Data = collect($chartData['data'])->firstWhere('label', '200 Level');
        $this->assertEquals(70.0, $level2Data['value']);
        $this->assertEquals(25, $level2Data['student_count']);
    }

    public function test_gets_attendance_chart_data_by_staff()
    {
        $department = Department::factory()->create();
        $lecturer1 = Lecturer::factory()->create(['department_id' => $department->id]);
        $lecturer2 = Lecturer::factory()->create(['department_id' => $department->id]);
        
        $user1 = User::factory()->create(['name' => 'Dr. Smith']);
        $user2 = User::factory()->create(['name' => 'Prof. Johnson']);
        
        $lecturer1->user()->associate($user1);
        $lecturer2->user()->associate($user2);
        $lecturer1->save();
        $lecturer2->save();

        // Create sessions for each lecturer
        $classroom1 = Classroom::factory()->create(['lecturer_id' => $lecturer1->id]);
        $classroom2 = Classroom::factory()->create(['lecturer_id' => $lecturer2->id]);
        
        AttendanceSession::factory()->count(5)->create([
            'classroom_id' => $classroom1->id,
            'created_at' => now()->subDays(10),
        ]);
        
        AttendanceSession::factory()->count(3)->create([
            'classroom_id' => $classroom2->id,
            'created_at' => now()->subDays(15),
        ]);

        // Create attendance records
        Attendance::factory()->count(50)->create([
            'created_at' => now()->subDays(10),
        ]);
        
        Attendance::factory()->count(30)->create([
            'created_at' => now()->subDays(15),
        ]);

        $chartData = $this->service->getAttendanceChartData($department->id, 'staff');

        $this->assertEquals('bar', $chartData['type']);
        $this->assertEquals('Sessions Conducted by Staff', $chartData['title']);
        $this->assertCount(2, $chartData['data']);
    }

    public function test_gets_performance_metrics()
    {
        $department = Department::factory()->create();
        $lecturer = Lecturer::factory()->create(['department_id' => $department->id]);
        $classroom = Classroom::factory()->create(['lecturer_id' => $lecturer->id]);

        // Create sessions with different characteristics
        AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'created_at' => now()->subDays(5),
            'end_time' => now()->subDays(5)->addMinutes(60),
            'is_punctual' => true,
            'is_out_of_bounds' => false,
        ]);

        AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'created_at' => now()->subDays(10),
            'end_time' => now()->subDays(10)->addMinutes(45),
            'is_punctual' => false,
            'is_out_of_bounds' => true,
        ]);

        AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'created_at' => now()->subDays(15),
            'end_time' => now()->subDays(15)->addMinutes(90),
            'is_punctual' => true,
            'is_out_of_bounds' => false,
        ]);

        $metrics = $this->service->getPerformanceMetrics($department->id, 30);

        $this->assertEquals(3, $metrics['total_sessions']);
        $this->assertEquals(65.0, $metrics['average_duration_minutes']); // (60+45+90)/3
        $this->assertEquals(66.67, $metrics['punctuality_rate']); // 2/3 * 100
        $this->assertEquals(66.67, $metrics['geofence_compliance']); // 2/3 * 100
        $this->assertEquals(1, $metrics['out_of_bounds_sessions']);
        $this->assertEquals(30, $metrics['period_days']);
    }

    public function test_gets_recent_activity()
    {
        $department = Department::factory()->create();
        $lecturer = Lecturer::factory()->create(['department_id' => $department->id]);
        $user = User::factory()->create(['name' => 'Dr. Smith']);
        $lecturer->user()->associate($user);
        $lecturer->save();
        
        $course = Course::factory()->create(['name' => 'Mathematics 101']);
        $classroom = Classroom::factory()->create([
            'lecturer_id' => $lecturer->id,
            'course_id' => $course->id,
        ]);

        // Create recent sessions
        $session1 = AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'created_at' => now()->subMinutes(30),
            'end_time' => null,
            'is_out_of_bounds' => false,
        ]);

        $session2 = AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'created_at' => now()->subHours(2),
            'end_time' => now()->subHours(1),
            'is_out_of_bounds' => true,
        ]);

        $activity = $this->service->getRecentActivity($department->id, 5);

        $this->assertCount(2, $activity);
        
        $firstActivity = $activity[0];
        $this->assertEquals('attendance_session', $firstActivity['type']);
        $this->assertEquals('Attendance Session Started', $firstActivity['title']);
        $this->assertStringContains('Dr. Smith', $firstActivity['description']);
        $this->assertStringContains('Mathematics 101', $firstActivity['description']);
        $this->assertEquals('Dr. Smith', $firstActivity['lecturer_name']);
        $this->assertEquals('Mathematics 101', $firstActivity['course_name']);
        $this->assertFalse($firstActivity['is_completed']);
        $this->assertFalse($firstActivity['is_out_of_bounds']);
        
        $secondActivity = $activity[1];
        $this->assertTrue($secondActivity['is_completed']);
        $this->assertTrue($secondActivity['is_out_of_bounds']);
    }

    public function test_caches_dashboard_data()
    {
        Cache::flush();
        
        $department = Department::factory()->create();
        Student::factory()->count(10)->create(['department_id' => $department->id]);
        
        $this->mockAttendanceService->shouldReceive('getDepartmentAttendanceSummary')
            ->once()
            ->andReturn([
                'total_students' => 10,
                'average_attendance' => 75.0,
                'excellent_count' => 5,
                'good_count' => 3,
                'warning_count' => 1,
                'critical_count' => 1,
            ]);

        // First call should hit the service
        $overview1 = $this->service->getDepartmentOverview($department->id);
        
        // Second call should use cache (service shouldn't be called again)
        $overview2 = $this->service->getDepartmentOverview($department->id);

        $this->assertEquals($overview1, $overview2);
        $this->assertEquals(10, $overview1['total_students']);
    }

    public function test_clears_cache()
    {
        $department = Department::factory()->create();
        
        // Set some cache values
        Cache::put("hod_dashboard_overview_{$department->id}", ['test' => 'data'], 600);
        Cache::put("hod_attendance_chart_{$department->id}_level_30", ['test' => 'data'], 600);
        
        $this->assertTrue(Cache::has("hod_dashboard_overview_{$department->id}"));
        $this->assertTrue(Cache::has("hod_attendance_chart_{$department->id}_level_30"));
        
        $this->service->clearCache($department->id);
        
        $this->assertFalse(Cache::has("hod_dashboard_overview_{$department->id}"));
        $this->assertFalse(Cache::has("hod_attendance_chart_{$department->id}_level_30"));
    }

    public function test_handles_empty_department()
    {
        $department = Department::factory()->create();
        
        $this->mockAttendanceService->shouldReceive('getDepartmentAttendanceSummary')
            ->with($department->id)
            ->andReturn([
                'total_students' => 0,
                'average_attendance' => 0,
                'excellent_count' => 0,
                'good_count' => 0,
                'warning_count' => 0,
                'critical_count' => 0,
            ]);

        $overview = $this->service->getDepartmentOverview($department->id);

        $this->assertEquals(0, $overview['total_students']);
        $this->assertEquals(0, $overview['total_lecturers']);
        $this->assertEquals(0, $overview['total_classes']);
        $this->assertEquals(0, $overview['average_attendance']);
        $this->assertEquals(0, $overview['active_sessions']);
    }
}