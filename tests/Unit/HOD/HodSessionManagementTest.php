<?php

namespace Tests\Unit\HOD;

use Tests\TestCase;
use App\Http\Middleware\HODSessionTimeout;
use App\Models\Hod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Session\Store;
use Mockery;

class HodSessionManagementTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_session_timeout_middleware_passes_for_active_session()
    {
        // Mock HOD
        $hod = Mockery::mock(Hod::class);
        $hod->shouldReceive('updateLastLogin')->once();
        
        // Mock Auth
        Auth::shouldReceive('guard')->with('hod')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($hod);
        
        // Mock session
        $session = Mockery::mock(Store::class);
        $currentTime = now()->timestamp;
        $session->shouldReceive('get')->with('hod_last_activity')->andReturn($currentTime - 300); // 5 minutes ago
        $session->shouldReceive('get')->with('hod_last_update', 0)->andReturn($currentTime - 400); // 6+ minutes ago
        $session->shouldReceive('put')->with('hod_last_activity', Mockery::type('int'));
        $session->shouldReceive('put')->with('hod_last_update', Mockery::type('int'));
        
        // Mock request
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('session')->andReturn($session);
        $request->shouldReceive('ajax')->andReturn(false);
        $request->shouldReceive('wantsJson')->andReturn(false);
        
        Config::shouldReceive('get')->with('auth.hod_session_timeout', 60)->andReturn(60);

        $middleware = new HODSessionTimeout();
        
        $response = $middleware->handle($request, function ($req) {
            return new Response('success');
        });

        $this->assertEquals('success', $response->getContent());
    }

    public function test_session_timeout_middleware_logs_out_expired_session()
    {
        // Mock HOD
        $hod = Mockery::mock(Hod::class);
        
        // Mock Auth
        Auth::shouldReceive('guard')->with('hod')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($hod);
        Auth::shouldReceive('logout')->once();
        
        // Mock session
        $session = Mockery::mock(Store::class);
        $currentTime = now()->timestamp;
        $expiredTime = $currentTime - (61 * 60); // 61 minutes ago (expired)
        $session->shouldReceive('get')->with('hod_last_activity')->andReturn($expiredTime);
        $session->shouldReceive('invalidate')->once();
        $session->shouldReceive('regenerateToken')->once();
        
        // Mock request
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('session')->andReturn($session);
        $request->shouldReceive('ajax')->andReturn(false);
        $request->shouldReceive('wantsJson')->andReturn(false);
        
        Config::shouldReceive('get')->with('auth.hod_session_timeout', 60)->andReturn(60);

        $middleware = new HODSessionTimeout();
        
        $response = $middleware->handle($request, function ($req) {
            return new Response('success');
        });

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_session_timeout_middleware_returns_json_for_ajax_expired_session()
    {
        // Mock HOD
        $hod = Mockery::mock(Hod::class);
        
        // Mock Auth
        Auth::shouldReceive('guard')->with('hod')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($hod);
        Auth::shouldReceive('logout')->once();
        
        // Mock session
        $session = Mockery::mock(Store::class);
        $currentTime = now()->timestamp;
        $expiredTime = $currentTime - (61 * 60); // 61 minutes ago (expired)
        $session->shouldReceive('get')->with('hod_last_activity')->andReturn($expiredTime);
        $session->shouldReceive('invalidate')->once();
        $session->shouldReceive('regenerateToken')->once();
        
        // Mock request
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('session')->andReturn($session);
        $request->shouldReceive('ajax')->andReturn(true);
        $request->shouldReceive('wantsJson')->andReturn(true);
        
        Config::shouldReceive('get')->with('auth.hod_session_timeout', 60)->andReturn(60);

        $middleware = new HODSessionTimeout();
        
        $response = $middleware->handle($request, function ($req) {
            return new Response('success');
        });

        $this->assertEquals(401, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Session expired', $responseData['error']);
    }

    public function test_session_timeout_middleware_passes_for_unauthenticated_user()
    {
        // Mock Auth
        Auth::shouldReceive('guard')->with('hod')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(false);
        
        // Mock request
        $request = Mockery::mock(Request::class);

        $middleware = new HODSessionTimeout();
        
        $response = $middleware->handle($request, function ($req) {
            return new Response('success');
        });

        $this->assertEquals('success', $response->getContent());
    }

    public function test_session_timeout_middleware_handles_no_last_activity()
    {
        // Mock HOD
        $hod = Mockery::mock(Hod::class);
        $hod->shouldReceive('updateLastLogin')->once();
        
        // Mock Auth
        Auth::shouldReceive('guard')->with('hod')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($hod);
        
        // Mock session
        $session = Mockery::mock(Store::class);
        $session->shouldReceive('get')->with('hod_last_activity')->andReturn(null);
        $session->shouldReceive('get')->with('hod_last_update', 0)->andReturn(0);
        $session->shouldReceive('put')->with('hod_last_activity', Mockery::type('int'));
        $session->shouldReceive('put')->with('hod_last_update', Mockery::type('int'));
        
        // Mock request
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('session')->andReturn($session);
        $request->shouldReceive('ajax')->andReturn(false);
        $request->shouldReceive('wantsJson')->andReturn(false);
        
        Config::shouldReceive('get')->with('auth.hod_session_timeout', 60)->andReturn(60);

        $middleware = new HODSessionTimeout();
        
        $response = $middleware->handle($request, function ($req) {
            return new Response('success');
        });

        $this->assertEquals('success', $response->getContent());
    }
}