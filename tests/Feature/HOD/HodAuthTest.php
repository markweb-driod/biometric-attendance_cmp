<?php

namespace Tests\Feature\HOD;

use Tests\TestCase;
use App\Models\Hod;
use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class HodAuthTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_hod_can_login_with_valid_credentials()
    {
        $response = $this->post(route('hod.login'), [
            'staff_id' => 'HOD001',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('hod.dashboard'));
        $this->assertTrue(Auth::guard('hod')->check());
        $this->assertEquals($this->hod->id, Auth::guard('hod')->id());
    }

    public function test_hod_cannot_login_with_invalid_staff_id()
    {
        $response = $this->post(route('hod.login'), [
            'staff_id' => 'INVALID',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['staff_id']);
        $this->assertFalse(Auth::guard('hod')->check());
    }

    public function test_hod_cannot_login_with_invalid_password()
    {
        $response = $this->post(route('hod.login'), [
            'staff_id' => 'HOD001',
            'password' => 'wrongpassword'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['password']);
        $this->assertFalse(Auth::guard('hod')->check());
    }

    public function test_inactive_hod_cannot_login()
    {
        $this->hod->update(['is_active' => false]);

        $response = $this->post(route('hod.login'), [
            'staff_id' => 'HOD001',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['staff_id']);
        $this->assertFalse(Auth::guard('hod')->check());
    }

    public function test_hod_login_updates_last_login_timestamp()
    {
        $this->assertNull($this->hod->last_login_at);

        $this->post(route('hod.login'), [
            'staff_id' => 'HOD001',
            'password' => 'password123'
        ]);

        $this->hod->refresh();
        $this->assertNotNull($this->hod->last_login_at);
    }

    public function test_hod_login_regenerates_session()
    {
        $response = $this->post(route('hod.login'), [
            'staff_id' => 'HOD001',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('hod.dashboard'));
        $this->assertTrue(Auth::guard('hod')->check());
    }

    public function test_hod_can_logout()
    {
        Auth::guard('hod')->login($this->hod);
        $this->assertTrue(Auth::guard('hod')->check());

        $response = $this->post(route('hod.logout'));

        $response->assertRedirect('/login');
        $this->assertFalse(Auth::guard('hod')->check());
    }

    public function test_hod_logout_invalidates_session()
    {
        Auth::guard('hod')->login($this->hod);
        
        $response = $this->post(route('hod.logout'));
        
        $response->assertRedirect('/login');
        $this->assertFalse(Auth::guard('hod')->check());
    }

    public function test_login_requires_staff_id()
    {
        $response = $this->post(route('hod.login'), [
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['staff_id']);
    }

    public function test_login_requires_password()
    {
        $response = $this->post(route('hod.login'), [
            'staff_id' => 'HOD001'
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_hod_without_user_cannot_login()
    {
        // Create HOD without associated user
        $hodWithoutUser = Hod::factory()->create([
            'user_id' => 999999, // Non-existent user
            'department_id' => $this->department->id,
            'staff_id' => 'HOD002',
            'is_active' => true
        ]);

        $response = $this->post(route('hod.login'), [
            'staff_id' => 'HOD002',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['staff_id']);
        $this->assertFalse(Auth::guard('hod')->check());
    }
}