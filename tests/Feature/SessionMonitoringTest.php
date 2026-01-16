<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UserSession;
use App\Models\Superadmin;
use App\Models\Lecturer;
use App\Models\Hod;
use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SessionMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;
    protected $lecturer;
    protected $hod;
    protected $department;

    protected function setUp(): void
    {
        parent::setUp();
        
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
        
        // Create lecturer user
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
        
        // Create HOD user
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

    public function test_superadmin_login_creates_session()
    {
        Mail::fake();
        
        $response = $this->post('/login', [
            'identifier' => 'admin@test.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        
        // Check session was created
        $session = UserSession::where('user_type', 'Superadmin')
            ->where('user_id', $this->superadmin->id)
            ->first();
            
        $this->assertNotNull($session);
        $this->assertEquals('active', $session->status);
        $this->assertEquals('Superadmin', $session->user_type);
        $this->assertEquals('admin@test.com', $session->identifier);
        
        // Check email notification was queued
        Mail::assertQueued(\App\Mail\LoginNotificationMail::class);
    }

    public function test_lecturer_login_creates_session()
    {
        Mail::fake();
        
        $response = $this->post('/login', [
            'identifier' => 'LEC001',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        
        // Check session was created
        $session = UserSession::where('user_type', 'Lecturer')
            ->where('user_id', $this->lecturer->id)
            ->first();
            
        $this->assertNotNull($session);
        $this->assertEquals('active', $session->status);
        $this->assertEquals('Lecturer', $session->user_type);
        $this->assertEquals('LEC001', $session->identifier);
        $this->assertEquals($this->department->id, $session->department_id);
        
        // Check email notification was queued
        Mail::assertQueued(\App\Mail\LoginNotificationMail::class);
    }

    public function test_hod_login_creates_session()
    {
        Mail::fake();
        
        $response = $this->post('/login', [
            'identifier' => 'HOD001',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        
        // Check session was created
        $session = UserSession::where('user_type', 'Hod')
            ->where('user_id', $this->hod->id)
            ->first();
            
        $this->assertNotNull($session);
        $this->assertEquals('active', $session->status);
        $this->assertEquals('Hod', $session->user_type);
        $this->assertEquals('HOD001', $session->identifier);
        $this->assertEquals($this->department->id, $session->department_id);
        
        // Check email notification was queued
        Mail::assertQueued(\App\Mail\LoginNotificationMail::class);
    }

    public function test_logout_marks_session_as_ended()
    {
        Mail::fake();
        
        // Login first (this creates a session via UnifiedAuthController)
        $response = $this->post('/login', [
            'identifier' => 'admin@test.com',
            'password' => 'password123'
        ]);
        
        // Find the created session
        $session = UserSession::where('user_type', 'Superadmin')
            ->where('user_id', $this->superadmin->id)
            ->where('status', 'active')
            ->first();
        
        $this->assertNotNull($session, 'Session should be created on login');
        
        // Get current session ID
        $currentSessionId = session()->getId();
        
        // Logout
        $response = $this->post('/superadmin/logout');
        
        $response->assertRedirect('/login');
        
        // Check session was marked as ended
        $session->refresh();
        $this->assertEquals('ended', $session->status);
        $this->assertNotNull($session->logout_at);
    }

    public function test_session_monitoring_dashboard_requires_authentication()
    {
        $response = $this->get('/superadmin/session-monitoring');
        
        $response->assertRedirect('/login');
    }

    public function test_authenticated_superadmin_can_access_session_monitoring()
    {
        Auth::guard('superadmin')->login($this->superadmin);
        
        $response = $this->get('/superadmin/session-monitoring');
        
        $response->assertStatus(200);
        $response->assertViewIs('superadmin.session-monitoring.index');
    }

    public function test_live_sessions_endpoint_returns_active_sessions()
    {
        Auth::guard('superadmin')->login($this->superadmin);
        
        // Create active sessions
        UserSession::create([
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
        
        UserSession::create([
            'session_id' => 'test-session-2',
            'user_type' => 'Lecturer',
            'user_id' => $this->lecturer->id,
            'identifier' => 'LEC001',
            'full_name' => 'Test Lecturer',
            'login_at' => now(),
            'last_activity_at' => now(),
            'status' => 'active',
            'ip_address' => '127.0.0.1',
            'department_id' => $this->department->id,
        ]);
        
        $response = $this->getJson('/superadmin/session-monitoring/live-sessions');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'sessions',
            'pagination'
        ]);
        
        $data = $response->json();
        $this->assertCount(2, $data['sessions']);
    }

    public function test_session_history_endpoint_returns_all_sessions()
    {
        Auth::guard('superadmin')->login($this->superadmin);
        
        // Create sessions with different statuses
        UserSession::create([
            'session_id' => 'test-session-1',
            'user_type' => 'Superadmin',
            'user_id' => $this->superadmin->id,
            'identifier' => $this->superadmin->email,
            'full_name' => $this->superadmin->full_name,
            'login_at' => now(),
            'logout_at' => now(),
            'status' => 'ended',
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
            'department_id' => $this->department->id,
        ]);
        
        $response = $this->getJson('/superadmin/session-monitoring/session-history');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'sessions',
            'pagination'
        ]);
        
        $data = $response->json();
        $this->assertCount(2, $data['sessions']);
    }

    public function test_session_details_endpoint_returns_session_information()
    {
        Auth::guard('superadmin')->login($this->superadmin);
        
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
            'device_type' => 'desktop',
            'browser' => 'Chrome',
            'os' => 'Windows',
        ]);
        
        $response = $this->getJson("/superadmin/session-monitoring/session/{$session->id}/details");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'session' => [
                'id',
                'user_type',
                'user_name',
                'identifier',
                'status',
                'login_at',
                'ip_address',
                'device_type',
                'browser',
                'os',
            ]
        ]);
    }

    public function test_terminate_session_endpoint_terminates_active_session()
    {
        Auth::guard('superadmin')->login($this->superadmin);
        
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
        
        $response = $this->postJson("/superadmin/session-monitoring/session/{$session->id}/terminate", [
            'reason' => 'Security concern'
        ]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $session->refresh();
        $this->assertEquals('terminated', $session->status);
        $this->assertNotNull($session->terminated_at);
        $this->assertEquals($this->superadmin->id, $session->terminated_by);
        $this->assertEquals('Security concern', $session->termination_reason);
    }

    public function test_session_history_filters_by_user_type()
    {
        Auth::guard('superadmin')->login($this->superadmin);
        
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
        
        $response = $this->getJson('/superadmin/session-monitoring/session-history?user_type=Lecturer');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertCount(1, $data['sessions']);
        $this->assertEquals('Lecturer', $data['sessions'][0]['user_type']);
    }

    public function test_session_history_filters_by_status()
    {
        Auth::guard('superadmin')->login($this->superadmin);
        
        UserSession::create([
            'session_id' => 'test-session-1',
            'user_type' => 'Superadmin',
            'user_id' => $this->superadmin->id,
            'identifier' => $this->superadmin->email,
            'full_name' => $this->superadmin->full_name,
            'login_at' => now(),
            'logout_at' => now(),
            'status' => 'ended',
            'ip_address' => '127.0.0.1',
        ]);
        
        UserSession::create([
            'session_id' => 'test-session-2',
            'user_type' => 'Superadmin',
            'user_id' => $this->superadmin->id,
            'identifier' => $this->superadmin->email,
            'full_name' => $this->superadmin->full_name,
            'login_at' => now(),
            'status' => 'active',
            'ip_address' => '127.0.0.1',
        ]);
        
        $response = $this->getJson('/superadmin/session-monitoring/session-history?status=ended');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertCount(1, $data['sessions']);
        $this->assertEquals('ended', $data['sessions'][0]['status']);
    }
}

