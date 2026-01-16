<?php

namespace Tests\Feature\HOD;

use Tests\TestCase;
use App\Models\Hod;
use App\Models\User;
use App\Models\Department;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class HodMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected $hod;
    protected $department;
    protected $otherDepartment;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test departments
        $this->department = Department::factory()->create([
            'name' => 'Computer Science',
            'code' => 'CSC'
        ]);
        
        $this->otherDepartment = Department::factory()->create([
            'name' => 'Mathematics',
            'code' => 'MTH'
        ]);
        
        // Create test user and HOD
        $user = User::factory()->create();
        $this->hod = Hod::factory()->create([
            'user_id' => $user->id,
            'department_id' => $this->department->id,
            'is_active' => true
        ]);

        // Define test routes for middleware testing
        Route::middleware(['hod.role'])->get('/test-hod-role', function () {
            return response('success');
        });

        Route::middleware(['hod.department'])->get('/test-department/{departmentId}', function ($departmentId) {
            return response('success');
        });

        Route::middleware(['hod.department'])->get('/test-student/{studentId}', function ($studentId) {
            return response('success');
        });

        Route::middleware(['hod.department'])->get('/test-lecturer/{lecturerId}', function ($lecturerId) {
            return response('success');
        });
    }

    public function test_ensure_hod_role_middleware_allows_authenticated_hod()
    {
        Auth::guard('hod')->login($this->hod);

        $response = $this->get('/test-hod-role');

        $response->assertStatus(200);
        $response->assertSee('success');
    }

    public function test_ensure_hod_role_middleware_redirects_unauthenticated_user()
    {
        $response = $this->get('/test-hod-role');

        $response->assertRedirect();
    }

    public function test_ensure_hod_role_middleware_blocks_inactive_hod()
    {
        $this->hod->update(['is_active' => false]);
        Auth::guard('hod')->login($this->hod);

        $response = $this->get('/test-hod-role');

        $response->assertRedirect();
        $this->assertFalse(Auth::guard('hod')->check());
    }

    public function test_verify_department_ownership_allows_own_department()
    {
        Auth::guard('hod')->login($this->hod);

        $response = $this->get('/test-department/' . $this->department->id);

        $response->assertStatus(200);
        $response->assertSee('success');
    }

    public function test_verify_department_ownership_blocks_other_department()
    {
        Auth::guard('hod')->login($this->hod);

        $response = $this->get('/test-department/' . $this->otherDepartment->id);

        $response->assertStatus(403);
    }

    public function test_verify_department_ownership_allows_own_department_student()
    {
        Auth::guard('hod')->login($this->hod);
        
        $student = Student::factory()->create([
            'department_id' => $this->department->id
        ]);

        $response = $this->get('/test-student/' . $student->id);

        $response->assertStatus(200);
        $response->assertSee('success');
    }

    public function test_verify_department_ownership_blocks_other_department_student()
    {
        Auth::guard('hod')->login($this->hod);
        
        $student = Student::factory()->create([
            'department_id' => $this->otherDepartment->id
        ]);

        $response = $this->get('/test-student/' . $student->id);

        $response->assertStatus(403);
    }

    public function test_verify_department_ownership_allows_own_department_lecturer()
    {
        Auth::guard('hod')->login($this->hod);
        
        $lecturer = Lecturer::factory()->create([
            'department_id' => $this->department->id
        ]);

        $response = $this->get('/test-lecturer/' . $lecturer->id);

        $response->assertStatus(200);
        $response->assertSee('success');
    }

    public function test_verify_department_ownership_blocks_other_department_lecturer()
    {
        Auth::guard('hod')->login($this->hod);
        
        $lecturer = Lecturer::factory()->create([
            'department_id' => $this->otherDepartment->id
        ]);

        $response = $this->get('/test-lecturer/' . $lecturer->id);

        $response->assertStatus(403);
    }

    public function test_verify_department_ownership_redirects_unauthenticated_user()
    {
        $response = $this->get('/test-department/' . $this->department->id);

        $response->assertRedirect();
    }

    public function test_verify_department_ownership_handles_nonexistent_student()
    {
        Auth::guard('hod')->login($this->hod);

        $response = $this->get('/test-student/999999');

        $response->assertStatus(200); // Should pass since student doesn't exist
    }

    public function test_verify_department_ownership_handles_nonexistent_lecturer()
    {
        Auth::guard('hod')->login($this->hod);

        $response = $this->get('/test-lecturer/999999');

        $response->assertStatus(200); // Should pass since lecturer doesn't exist
    }
}