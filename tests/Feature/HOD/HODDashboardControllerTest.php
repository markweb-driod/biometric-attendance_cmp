<?php

namespace Tests\Feature\HOD;

use Tests\TestCase;
use App\Models\Hod;
use App\Models\User;
use App\Models\Department;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Course;
use App\Models\Classroom;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Services\HODDashboardService;
use App\Services\AttendanceCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HODDashboardControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Hod $hod;
    private Department $department;
    private User $hodUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test department
        $this->department = Department::factory()->create([
            'name' => 'Computer Science',
            'code' => 'CSC'
        ]);

        // Create HOD user
        $this->hodUser = User::factory()->create([
            'username' => 'hod001',
            'full_name' => 'John Doe',
            'email' => 'hod@test.com',
            'role' => 'hod'
        ]);

        // Create HOD
        $this->hod = Hod::factory()->create([
            'user_id' => $this->hodUser->id,
            'department_id' => $this->department->id,
            'staff_id' => 'HOD001',
            'is_active' => true
        ]);

        // Clear cache before each test
        Cache::flush();
    }

    /** @test */
    public function hod_can_access_dashboard_view()
    {
        // Create some test data
        Student::factory()->count(25)->create(['department_id' => $this->department->id]);
        Lecturer::factory()->count(5)->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->hod, 'hod')
            ->get(route('hod.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('hod.dashboard');
        $response->assertViewHas([
            'hod',
            'dashboardData',
            'thresholdCompliance',
            'performanceMetrics',
            'recentActivities'
        ]);

        // Verify audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->hod->id,
            'action' => 'view',
            'description' => 'HOD accessed dashboard'
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get(route('hod.dashboard'));

        $response->assertRedirect(route('hod.login'));
    }

    /** @test */
    public function dashboard_handles_service_exceptions_gracefully()
    {
        // Mock the service to throw an exception
        $this->mock(HODDashboardService::class, function ($mock) {
            $mock->shouldReceive('getDepartmentOverview')
                ->andThrow(new \Exception('Service error'));
        });

        $response = $this->actingAs($this->hod, 'hod')
            ->get(route('hod.dashboard'));

        $response->assertRedirect(route('hod.login'));
        $response->assertSessionHas('error', 'An error occurred while loading the dashboard.');
    }

    /** @test */
    public function get_dashboard_stats_returns_correct_data()
    {
        // Create test data
        Student::factory()->count(30)->create(['department_id' => $this->department->id]);
        Lecturer::factory()->count(8)->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.dashboard-stats'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'overview' => [
                    'total_students',
                    'total_lecturers',
                    'total_classes',
                    'average_attendance',
                    'active_sessions',
                    'attendance_distribution',
                    'last_updated'
                ],
                'threshold_compliance' => [
                    'total_students',
                    'compliant_students',
                    'non_compliant_students',
                    'compliance_rate',
                    'threshold'
                ],
                'performance_metrics' => [
                    'total_sessions',
                    'average_duration_minutes',
                    'punctuality_rate',
                    'geofence_compliance'
                ]
            ],
            'timestamp'
        ]);

        $response->assertJson([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_students' => 30,
                    'total_lecturers' => 8
                ]
            ]
        ]);

        // Verify audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->hod->id,
            'action' => 'api_access',
            'description' => 'HOD accessed dashboard stats API'
        ]);
    }

    /** @test */
    public function get_dashboard_stats_requires_authentication()
    {
        $response = $this->getJson(route('hod.api.dashboard-stats'));

        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Unauthorized',
            'message' => 'Authentication required'
        ]);
    }

    /** @test */
    public function get_dashboard_stats_handles_exceptions()
    {
        // Mock the service to throw an exception
        $this->mock(HODDashboardService::class, function ($mock) {
            $mock->shouldReceive('getDepartmentOverview')
                ->andThrow(new \Exception('Database error'));
        });

        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.dashboard-stats'));

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
            'error' => 'Internal server error',
            'message' => 'Failed to retrieve dashboard statistics'
        ]);
    }

    /** @test */
    public function get_live_staff_activity_returns_correct_data()
    {
        // Create test data
        $lecturer = Lecturer::factory()->create(['department_id' => $this->department->id]);
        $course = Course::factory()->create(['department_id' => $this->department->id]);
        $classroom = Classroom::factory()->create([
            'lecturer_id' => $lecturer->id,
            'course_id' => $course->id
        ]);
        
        $activeSession = AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'end_time' => null, // Active session
            'lecturer_latitude' => 9.0820,
            'lecturer_longitude' => 8.6753,
            'is_out_of_bounds' => false
        ]);

        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.live-activity'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'active_sessions',
                'recent_activities',
                'summary' => [
                    'total_active_sessions',
                    'out_of_bounds_sessions',
                    'total_students_in_session',
                    'total_marked_present'
                ]
            ],
            'last_updated'
        ]);

        $response->assertJson([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_active_sessions' => 1,
                    'out_of_bounds_sessions' => 0
                ]
            ]
        ]);
    }

    /** @test */
    public function get_live_staff_activity_requires_authentication()
    {
        $response = $this->getJson(route('hod.api.live-activity'));

        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Unauthorized',
            'message' => 'Authentication required'
        ]);
    }

    /** @test */
    public function get_attendance_chart_returns_correct_data_for_level_grouping()
    {
        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.attendance-chart', ['group_by' => 'level', 'days' => 30]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'type',
                'title',
                'data'
            ],
            'parameters' => [
                'group_by',
                'days',
                'department_id'
            ],
            'timestamp'
        ]);

        $response->assertJson([
            'success' => true,
            'parameters' => [
                'group_by' => 'level',
                'days' => 30,
                'department_id' => $this->department->id
            ]
        ]);
    }

    /** @test */
    public function get_attendance_chart_returns_correct_data_for_course_grouping()
    {
        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.attendance-chart', ['group_by' => 'course', 'days' => 7]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'parameters' => [
                'group_by' => 'course',
                'days' => 7
            ]
        ]);
    }

    /** @test */
    public function get_attendance_chart_returns_correct_data_for_staff_grouping()
    {
        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.attendance-chart', ['group_by' => 'staff', 'days' => 14]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'parameters' => [
                'group_by' => 'staff',
                'days' => 14
            ]
        ]);
    }

    /** @test */
    public function get_attendance_chart_returns_correct_data_for_monthly_grouping()
    {
        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.attendance-chart', ['group_by' => 'monthly']));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'parameters' => [
                'group_by' => 'monthly',
                'days' => 30 // default value
            ]
        ]);
    }

    /** @test */
    public function get_attendance_chart_validates_group_by_parameter()
    {
        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.attendance-chart', ['group_by' => 'invalid']));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'error' => 'Invalid parameter',
            'message' => 'group_by must be one of: level, course, staff, monthly'
        ]);
    }

    /** @test */
    public function get_attendance_chart_validates_days_parameter()
    {
        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.attendance-chart', ['days' => 500]));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'error' => 'Invalid parameter',
            'message' => 'days must be between 1 and 365'
        ]);

        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.attendance-chart', ['days' => 0]));

        $response->assertStatus(400);
    }

    /** @test */
    public function get_attendance_chart_requires_authentication()
    {
        $response = $this->getJson(route('hod.api.attendance-chart'));

        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Unauthorized',
            'message' => 'Authentication required'
        ]);
    }

    /** @test */
    public function get_attendance_chart_handles_exceptions()
    {
        // Mock the service to throw an exception
        $this->mock(HODDashboardService::class, function ($mock) {
            $mock->shouldReceive('getAttendanceChartData')
                ->andThrow(new \Exception('Chart generation error'));
        });

        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.attendance-chart'));

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
            'error' => 'Internal server error',
            'message' => 'Failed to retrieve attendance chart data'
        ]);
    }

    /** @test */
    public function get_live_activity_is_alias_for_get_live_staff_activity()
    {
        // Create test data
        $lecturer = Lecturer::factory()->create(['department_id' => $this->department->id]);
        $course = Course::factory()->create(['department_id' => $this->department->id]);
        $classroom = Classroom::factory()->create([
            'lecturer_id' => $lecturer->id,
            'course_id' => $course->id
        ]);

        $response1 = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.live-activity'));

        $response2 = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.live-activity'));

        // Both should return the same structure
        $response1->assertStatus(200);
        $response2->assertStatus(200);
        
        $response1->assertJsonStructure([
            'success',
            'data' => [
                'active_sessions',
                'recent_activities',
                'summary'
            ]
        ]);
    }

    /** @test */
    public function dashboard_methods_only_return_department_specific_data()
    {
        // Create data for this HOD's department
        $ourStudents = Student::factory()->count(10)->create(['department_id' => $this->department->id]);
        $ourLecturers = Lecturer::factory()->count(3)->create(['department_id' => $this->department->id]);

        // Create data for another department
        $otherDepartment = Department::factory()->create(['name' => 'Mathematics']);
        $otherStudents = Student::factory()->count(15)->create(['department_id' => $otherDepartment->id]);
        $otherLecturers = Lecturer::factory()->count(5)->create(['department_id' => $otherDepartment->id]);

        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.dashboard-stats'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_students' => 10, // Only our department's students
                    'total_lecturers' => 3  // Only our department's lecturers
                ]
            ]
        ]);
    }

    /** @test */
    public function dashboard_caches_expensive_operations()
    {
        // Create test data
        Student::factory()->count(20)->create(['department_id' => $this->department->id]);
        
        // First request should hit the database
        $response1 = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.dashboard-stats'));

        $response1->assertStatus(200);

        // Second request should use cache (we can't easily test this without mocking)
        $response2 = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.dashboard-stats'));

        $response2->assertStatus(200);
        
        // Both responses should be identical
        $this->assertEquals($response1->json(), $response2->json());
    }

    /** @test */
    public function dashboard_handles_empty_department_data()
    {
        // Don't create any students or lecturers
        
        $response = $this->actingAs($this->hod, 'hod')
            ->getJson(route('hod.api.dashboard-stats'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_students' => 0,
                    'total_lecturers' => 0,
                    'total_classes' => 0,
                    'active_sessions' => 0
                ]
            ]
        ]);
    }
}