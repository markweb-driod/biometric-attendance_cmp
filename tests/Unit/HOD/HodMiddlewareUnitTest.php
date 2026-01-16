<?php

namespace Tests\Unit\HOD;

use Tests\TestCase;
use App\Http\Middleware\EnsureHODRole;
use App\Http\Middleware\VerifyDepartmentOwnership;
use App\Models\Hod;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Mockery;

class HodMiddlewareUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_ensure_hod_role_middleware_passes_for_authenticated_active_hod()
    {
        // Mock the HOD guard
        $hod = Mockery::mock(Hod::class);
        $hod->shouldReceive('getAttribute')->with('is_active')->andReturn(true);
        
        Auth::shouldReceive('guard')->with('hod')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($hod);

        $middleware = new EnsureHODRole();
        $request = Request::create('/test');
        
        $response = $middleware->handle($request, function ($req) {
            return new Response('success');
        });

        $this->assertEquals('success', $response->getContent());
    }

    public function test_ensure_hod_role_middleware_redirects_for_unauthenticated_user()
    {
        Auth::shouldReceive('guard')->with('hod')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(false);

        $middleware = new EnsureHODRole();
        $request = Request::create('/test');
        
        $response = $middleware->handle($request, function ($req) {
            return new Response('success');
        });

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_ensure_hod_role_middleware_redirects_for_inactive_hod()
    {
        // Mock the HOD guard
        $hod = Mockery::mock(Hod::class);
        $hod->shouldReceive('getAttribute')->with('is_active')->andReturn(false);
        
        Auth::shouldReceive('guard')->with('hod')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($hod);
        Auth::shouldReceive('logout')->once();

        $middleware = new EnsureHODRole();
        $request = Request::create('/test');
        
        $response = $middleware->handle($request, function ($req) {
            return new Response('success');
        });

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_verify_department_ownership_middleware_passes_for_same_department()
    {
        $hod = Mockery::mock(Hod::class);
        $hod->shouldReceive('getAttribute')->with('department_id')->andReturn(1);
        
        Auth::shouldReceive('guard')->with('hod')->andReturnSelf();
        Auth::shouldReceive('user')->andReturn($hod);

        $middleware = new VerifyDepartmentOwnership();
        $request = Request::create('/test');
        $request->merge(['department_id' => 1]);
        
        $response = $middleware->handle($request, function ($req) {
            return new Response('success');
        });

        $this->assertEquals('success', $response->getContent());
    }

    public function test_verify_department_ownership_middleware_blocks_different_department()
    {
        $hod = Mockery::mock(Hod::class);
        $hod->shouldReceive('getAttribute')->with('department_id')->andReturn(1);
        
        Auth::shouldReceive('guard')->with('hod')->andReturnSelf();
        Auth::shouldReceive('user')->andReturn($hod);

        $middleware = new VerifyDepartmentOwnership();
        $request = Request::create('/test');
        $request->merge(['department_id' => 2]);
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('You do not have permission to access resources from this department.');
        
        $middleware->handle($request, function ($req) {
            return new Response('success');
        });
    }

    public function test_verify_department_ownership_middleware_redirects_unauthenticated_user()
    {
        Auth::shouldReceive('guard')->with('hod')->andReturnSelf();
        Auth::shouldReceive('user')->andReturn(null);

        $middleware = new VerifyDepartmentOwnership();
        $request = Request::create('/test');
        
        $response = $middleware->handle($request, function ($req) {
            return new Response('success');
        });

        $this->assertEquals(302, $response->getStatusCode());
    }
}