<?php

namespace Tests\Unit\HOD;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class HodSessionConfigTest extends TestCase
{
    public function test_hod_session_timeout_configuration_exists()
    {
        $timeout = Config::get('auth.hod_session_timeout');
        $this->assertNotNull($timeout);
        $this->assertIsInt($timeout);
        $this->assertGreaterThan(0, $timeout);
    }

    public function test_hod_session_timeout_has_default_value()
    {
        $timeout = Config::get('auth.hod_session_timeout', 60);
        $this->assertEquals(60, $timeout);
    }

    public function test_hod_authentication_guard_exists()
    {
        $guards = Config::get('auth.guards');
        $this->assertArrayHasKey('hod', $guards);
        $this->assertEquals('session', $guards['hod']['driver']);
        $this->assertEquals('hods', $guards['hod']['provider']);
    }

    public function test_hod_provider_configuration_exists()
    {
        $providers = Config::get('auth.providers');
        $this->assertArrayHasKey('hods', $providers);
        $this->assertEquals('eloquent', $providers['hods']['driver']);
        $this->assertEquals('App\Models\Hod', $providers['hods']['model']);
    }
}