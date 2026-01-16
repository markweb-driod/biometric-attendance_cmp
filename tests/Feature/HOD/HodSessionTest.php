<?php

namespace Tests\Feature\HOD;

use Tests\TestCase;
use App\Models\Hod;
use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class HodSessionTest extends TestCase
{
    use RefreshDatabase;

    protected $hod;
    protected $user;
    protected $department;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test department
        $this->department = Department::factory()->create([
            'name' => 'Computer Science',
            'code' => 'CSC'
        ]);
        
        // Create test user
        $this->user = User::factory()->create([
            'email' => 'hod@test.com',
            'password' => Hash::make('password123'),
            'full_name' => 'Test HOD'
        ]);
        
        // Create test HOD
        $this->hod = Hod::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id,
            'staff_id' => 'HOD001',
            'is_active' => true
        ]);
    }

    public function test_hod_login_initializes_session_tracking()
    {
        $response = $this->post(route('hod.login.post'), [
            'staff_id' => 'HOD001',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('hod.dashboard'));
        
        // Check that session tracking is initialized
        $this->assertNotNull(session('hod_last_activity'));
        $this->assertNotNull(session('hod_last_update'));
        $this->assertTrue(Auth::guard('hod')->check());
    }

    public function test_hod_login_regenerates_session()
    {
        // Get initial session ID
        $initialSessionId = session()->getId();
        
        $response = $this->post(route('hod.login.post'), [
            'staff_id' => 'HOD001',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('hod.dashboard'));
        
        // Session should be regenerated (new ID)
        $this->assertNotEquals($initialSessionId, session()->getId());
    }

    public function test_hod_login_updates_last_login_timestamp()
    {
        $originalLastLogin = $this->hod->last_login_at;
        
        $this->post(route('hod.login.post'), [
            'staff_id' => 'HOD001',
            'password' => 'password123'
        ]);

        $this->hod->refresh();
        $this->assertNotEquals($originalLastLogin, $this->hod->last_login_at);
        $this->assertNotNull($this->hod->last_login_at);
    }

    public function test_hod_logout_invalidates_session()
    {
        // Login first
        Auth::guard('hod')->login($this->hod);
        $this->assertTrue(Auth::guard('hod')->check());
        
        // Set session data
        session(['hod_last_activity' => now()->timestamp]);
        $this->assertNotNull(session('hod_last_activity'));
        
        // Logout
        $response = $this->post(route('hod.logout'));
        
        $response->assertRedirect('/login');
        $this->assertFalse(Auth::guard('hod')->check());
        $this->assertNull(session('hod_last_activity'));
    }

    public function test_session_timeout_configuration_exists()
    {
        $timeout = Config::get('auth.hod_session_timeout');
        $this->assertNotNull($timeout);
        $this->assertIsInt($timeout);
        $this->assertGreaterThan(0, $timeout);
    }

    public function test_protected_route_requires_authentication()
    {
        $response = $this->get(route('hod.dashboard'));
        
        $response->assertRedirect();
    }

    public function test_authenticated_hod_can_access_protected_routes()
    {
        Auth::guard('hod')->login($this->hod);
        
        // Set session activity to simulate active session
        session(['hod_last_activity' => now()->timestamp]);
        
        $response = $this->get(route('hod.dashboard'));
        
        $response->assertStatus(200);
    }
}