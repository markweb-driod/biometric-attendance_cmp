<?php

namespace Tests\Feature\HOD;

use Tests\TestCase;
use App\Models\Hod;
use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HODDashboardControllerSimpleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function basic_test_setup_works()
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

        $this->assertDatabaseHas('users', [
            'email' => 'hod@test.com',
            'full_name' => 'John Doe'
        ]);

        $this->assertDatabaseHas('departments', [
            'name' => 'Computer Science',
            'code' => 'CSC'
        ]);

        $this->assertDatabaseHas('hods', [
            'staff_id' => 'HOD001',
            'user_id' => $hodUser->id,
            'department_id' => $department->id
        ]);
    }
}