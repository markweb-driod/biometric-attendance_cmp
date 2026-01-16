<?php

namespace Tests\Feature\HOD;

use Tests\TestCase;
use App\Models\Hod;
use App\Models\User;
use App\Models\Department;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HODDashboardControllerBasicTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function hod_can_access_dashboard_view()
    {
        // Create test department
        $department = Department::factory()->create([
            'name' => 'Computer Science',
            'code' => 'CSC'
        ]);

        // Create HOD user
        $hodUser = User::factory()->create([
            'username' => 'hod001',
            'full_name' => 'John Doe',
            'email' => 'hod@test.com',
            'role' => 'hod'
        ]);

        // Create HOD
        $hod = Hod::factory()->create([
            'user_id' => $hodUser->id,
            'department_id' => $department->id,
            'staff_id' => 'HOD001',
            'is_active' => true
        ]);

        // Create some test data
        Student::factory()->count(5)->create(['department_id' => $department->id]);
        Lecturer::factory()->count(2)->create(['department_id' => $department->id]);

        $response = $this->actingAs($hod, 'hod')
            ->get(route('hod.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('hod.dashboard');
    }

    /** @test */
    public function get_dashboard_stats_returns_json()
    {
        // Create test department
        $department = Department::factory()->create();

        // Create HOD user
        $hodUser = User::factory()->create(['role' => 'hod']);

        // Create HOD
        $hod = Hod::factory()->create([
            'user_id' => $hodUser->id,
            'department_id' => $department->id,
            'is_active' => true
        ]);

        $response = $this->actingAs($hod, 'hod')
            ->getJson(route('hod.api.dashboard-stats'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'timestamp'
        ]);
    }
}