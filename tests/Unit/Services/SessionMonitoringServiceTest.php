<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\UserSession;
use App\Models\Superadmin;
use App\Models\Lecturer;
use App\Models\Hod;
use App\Models\User;
use App\Models\Department;
use App\Services\SessionMonitoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SessionMonitoringServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $superadmin;
    protected $lecturer;
    protected $hod;
    protected $department;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new SessionMonitoringService();
        
        // Create test department
        $this->department = Department::factory()->create([
            'name' => 'Computer Science',
            'code' => 'CSC'
        ]);
        
        // Create superadmin
        $this->superadmin = Superadmin::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'full_name' => 'Test Admin',
            'is_active' => true
        ]);
        
        // Create lecturer
        $lecturerUser = User::factory()->create([
            'email' => 'lecturer@test.com',
            'password' => Hash::make('password123'),
            'full_name' => 'Test Lecturer'
        ]);
        
        $this->lecturer = Lecturer::factory()->create([
            'user_id' => $lecturerUser->id,
            'department_id' => $this->department->id,
            'staff_id' => 'LEC001',
            'is_active' => true
        ]);
        
        // Create HOD
        $hodUser = User::factory()->create([
            'email' => 'hod@test.com',
            'password' => Hash::make('password123'),
            'full_name' => 'Test HOD'
        ]);
        
        $this->hod = Hod::factory()->create([
            'user_id' => $hodUser->id,
            'department_id' => $this->department->id,
            'staff_id' => 'HOD001',
            'is_active' => true
        ]);
    }

    public function test_create_session_for_superadmin()
    {
        $this->simulateRequest();
        
        $session = $this->service->createSession($this->superadmin, 'Superadmin');
        
        $this->assertNotNull($session);
        $this->assertEquals('Superadmin', $session->user_type);
        $this->assertEquals($this->superadmin->id, $session->user_id);
        $this->assertEquals($this->superadmin->email, $session->identifier);
        $this->assertEquals('active', $session->status);
        $this->assertNotNull($session->login_at);
    }

    public function test_create_session_for_lecturer()
    {
        $this->simulateRequest();
        
        $session = $this->service->createSession($this->lecturer, 'Lecturer');
        
        $this->assertNotNull($session);
        $this->assertEquals('Lecturer', $session->user_type);
        $this->assertEquals($this->lecturer->id, $session->user_id);
        $this->assertEquals('LEC001', $session->identifier);
        $this->assertEquals($this->department->id, $session->department_id);
        $this->assertEquals('active', $session->status);
    }

    public function test_create_session_for_hod()
    {
        $this->simulateRequest();
        
        $session = $this->service->createSession($this->hod, 'Hod');
        
        $this->assertNotNull($session);
        $this->assertEquals('Hod', $session->user_type);
        $this->assertEquals($this->hod->id, $session->user_id);
        $this->assertEquals('HOD001', $session->identifier);
        $this->assertEquals($this->department->id, $session->department_id);
        $this->assertEquals('active', $session->status);
    }

    public function test_end_session_marks_as_ended()
    {
        $session = UserSession::create([
            'session_id' => 'test-session-1',
            'user_type' => 'Superadmin',
            'user_id' => $this->superadmin->id,
            'identifier' => $this->superadmin->email,
            'full_name' => $this->superadmin->full_name,
            'login_at' => now(),
            'last_activity_at' => now(),
            'status' => 'active',
            'ip_address' => '127.0.0.1',
        ]);
        
        $result = $this->service->endSession($session->session_id);
        
        $this->assertTrue($result);
        $session->refresh();
        $this->assertEquals('ended', $session->status);
        $this->assertNotNull($session->logout_at);
    }

    public function test_terminate_session_marks_as_terminated()
    {
        $session = UserSession::create([
            'session_id' => 'test-session-1',
            'user_type' => 'Lecturer',
            'user_id' => $this->lecturer->id,
            'identifier' => 'LEC001',
            'full_name' => 'Test Lecturer',
            'login_at' => now(),
            'last_activity_at' => now(),
            'status' => 'active',
            'ip_address' => '127.0.0.1',
        ]);
        
        $terminatedBy = $this->superadmin->id;
        $reason = 'Security violation';
        
        $result = $this->service->terminateSession($session->id, $terminatedBy, $reason);
        
        $this->assertTrue($result);
        $session->refresh();
        $this->assertEquals('terminated', $session->status);
        $this->assertEquals($terminatedBy, $session->terminated_by);
        $this->assertEquals($reason, $session->termination_reason);
        $this->assertNotNull($session->terminated_at);
    }

    public function test_get_active_sessions_count()
    {
        UserSession::create([
            'session_id' => 'test-session-1',
            'user_type' => 'Superadmin',
            'user_id' => $this->superadmin->id,
            'identifier' => $this->superadmin->email,
            'full_name' => $this->superadmin->full_name,
            'login_at' => now(),
            'status' => 'active',
            'ip_address' => '127.0.0.1',
        ]);
        
        UserSession::create([
            'session_id' => 'test-session-2',
            'user_type' => 'Lecturer',
            'user_id' => $this->lecturer->id,
            'identifier' => 'LEC001',
            'full_name' => 'Test Lecturer',
            'login_at' => now(),
            'status' => 'active',
            'ip_address' => '127.0.0.1',
        ]);
        
        UserSession::create([
            'session_id' => 'test-session-3',
            'user_type' => 'Superadmin',
            'user_id' => $this->superadmin->id,
            'identifier' => $this->superadmin->email,
            'full_name' => $this->superadmin->full_name,
            'login_at' => now(),
            'logout_at' => now(),
            'status' => 'ended',
            'ip_address' => '127.0.0.1',
        ]);
        
        $count = $this->service->getActiveSessionsCount();
        $this->assertEquals(2, $count);
        
        $superadminCount = $this->service->getActiveSessionsCount('Superadmin');
        $this->assertEquals(1, $superadminCount);
    }

    public function test_get_recent_stats()
    {
        UserSession::create([
            'session_id' => 'test-session-1',
            'user_type' => 'Superadmin',
            'user_id' => $this->superadmin->id,
            'identifier' => $this->superadmin->email,
            'full_name' => $this->superadmin->full_name,
            'login_at' => now(),
            'status' => 'active',
            'ip_address' => '127.0.0.1',
        ]);
        
        UserSession::create([
            'session_id' => 'test-session-2',
            'user_type' => 'Lecturer',
            'user_id' => $this->lecturer->id,
            'identifier' => 'LEC001',
            'full_name' => 'Test Lecturer',
            'login_at' => now(),
            'status' => 'active',
            'ip_address' => '127.0.0.1',
        ]);
        
        $stats = $this->service->getRecentStats(7);
        
        $this->assertEquals(2, $stats['total_sessions']);
        $this->assertEquals(1, $stats['superadmin_sessions']);
        $this->assertEquals(1, $stats['lecturer_sessions']);
    }

    private function simulateRequest()
    {
        // Mock request data for service
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
    }
}

