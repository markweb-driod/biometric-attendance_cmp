<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\GeoLocationService;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\Lecturer;
use App\Models\Department;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class GeoLocationServiceTest extends TestCase
{
    use RefreshDatabase;

    private GeoLocationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GeoLocationService();
    }

    public function test_calculates_distance_correctly()
    {
        // Test with known coordinates (approximately 1km apart)
        $lat1 = 9.0820; // NSUK coordinates
        $lon1 = 8.6753;
        $lat2 = 9.0910; // About 1km north
        $lon2 = 8.6753;

        $distance = $this->service->calculateDistance($lat1, $lon1, $lat2, $lon2);

        // Should be approximately 1km (allowing for some precision variance)
        $this->assertGreaterThan(0.9, $distance);
        $this->assertLessThan(1.1, $distance);
    }

    public function test_verifies_location_within_geofence()
    {
        // Classroom location
        $classLat = 9.0820;
        $classLon = 8.6753;

        // Lecturer location (within 500m)
        $lecturerLat = 9.0825;
        $lecturerLon = 8.6758;

        $result = $this->service->verifyGeoFence($classLat, $classLon, $lecturerLat, $lecturerLon, 0.5);

        $this->assertTrue($result);
    }

    public function test_verifies_location_outside_geofence()
    {
        // Classroom location
        $classLat = 9.0820;
        $classLon = 8.6753;

        // Lecturer location (more than 1km away)
        $lecturerLat = 9.0920;
        $lecturerLon = 8.6853;

        $result = $this->service->verifyGeoFence($classLat, $classLon, $lecturerLat, $lecturerLon, 0.5);

        $this->assertFalse($result);
    }

    public function test_flags_out_of_bounds_session()
    {
        Log::fake();

        $department = Department::factory()->create();
        $lecturer = Lecturer::factory()->create(['department_id' => $department->id]);
        $classroom = Classroom::factory()->create([
            'latitude' => 9.0820,
            'longitude' => 8.6753,
        ]);

        $session = AttendanceSession::factory()->create([
            'lecturer_id' => $lecturer->id,
            'classroom_id' => $classroom->id,
            'lecturer_latitude' => 9.0920, // Far from classroom
            'lecturer_longitude' => 8.6853,
            'is_out_of_bounds' => false,
        ]);

        $this->service->flagOutOfBoundsSession($session, 1.5, 0.5);

        // Refresh the session from database
        $session->refresh();

        $this->assertTrue($session->is_out_of_bounds);
        $this->assertEquals(1.5, $session->distance_from_classroom);
        $this->assertNotNull($session->flagged_at);

        // Check audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $lecturer->id,
            'action' => 'out_of_bounds_session',
            'status' => 'flagged',
        ]);

        // Check log was written
        Log::assertLogged('warning', function ($message, $context) use ($session) {
            return $message === 'Out of bounds attendance session detected' &&
                   $context['session_id'] === $session->id;
        });
    }

    public function test_validates_session_location_with_valid_location()
    {
        $classroom = Classroom::factory()->create([
            'latitude' => 9.0820,
            'longitude' => 8.6753,
        ]);

        $session = AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'lecturer_latitude' => 9.0825, // Close to classroom
            'lecturer_longitude' => 8.6758,
        ]);

        $result = $this->service->validateSessionLocation($session, 0.5);

        $this->assertTrue($result['is_valid']);
        $this->assertEquals('Within geofence', $result['reason']);
        $this->assertLessThan(0.5, $result['distance']);
        $this->assertEquals(0.5, $result['allowed_radius']);
    }

    public function test_validates_session_location_with_invalid_location()
    {
        $classroom = Classroom::factory()->create([
            'latitude' => 9.0820,
            'longitude' => 8.6753,
        ]);

        $session = AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'lecturer_latitude' => 9.0920, // Far from classroom
            'lecturer_longitude' => 8.6853,
        ]);

        $result = $this->service->validateSessionLocation($session, 0.5);

        $this->assertFalse($result['is_valid']);
        $this->assertEquals('Outside geofence', $result['reason']);
        $this->assertGreaterThan(0.5, $result['distance']);
        $this->assertEquals(0.5, $result['allowed_radius']);

        // Session should be flagged
        $session->refresh();
        $this->assertTrue($session->is_out_of_bounds);
    }

    public function test_validates_session_location_with_missing_lecturer_location()
    {
        $classroom = Classroom::factory()->create([
            'latitude' => 9.0820,
            'longitude' => 8.6753,
        ]);

        $session = AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'lecturer_latitude' => null,
            'lecturer_longitude' => null,
        ]);

        $result = $this->service->validateSessionLocation($session);

        $this->assertFalse($result['is_valid']);
        $this->assertEquals('Missing lecturer location data', $result['reason']);
        $this->assertNull($result['distance']);
    }

    public function test_validates_session_location_with_missing_classroom_location()
    {
        $classroom = Classroom::factory()->create([
            'latitude' => null,
            'longitude' => null,
        ]);

        $session = AttendanceSession::factory()->create([
            'classroom_id' => $classroom->id,
            'lecturer_latitude' => 9.0825,
            'lecturer_longitude' => 8.6758,
        ]);

        $result = $this->service->validateSessionLocation($session);

        $this->assertFalse($result['is_valid']);
        $this->assertEquals('Missing classroom location data', $result['reason']);
        $this->assertNull($result['distance']);
    }

    public function test_gets_out_of_bounds_sessions_for_department()
    {
        $department = Department::factory()->create();
        $lecturer = Lecturer::factory()->create(['department_id' => $department->id]);
        $classroom = Classroom::factory()->create(['lecturer_id' => $lecturer->id]);

        // Create some sessions - some out of bounds, some not
        $outOfBoundsSession1 = AttendanceSession::factory()->create([
            'lecturer_id' => $lecturer->id,
            'classroom_id' => $classroom->id,
            'is_out_of_bounds' => true,
            'created_at' => now()->subDays(5),
        ]);

        $outOfBoundsSession2 = AttendanceSession::factory()->create([
            'lecturer_id' => $lecturer->id,
            'classroom_id' => $classroom->id,
            'is_out_of_bounds' => true,
            'created_at' => now()->subDays(10),
        ]);

        $normalSession = AttendanceSession::factory()->create([
            'lecturer_id' => $lecturer->id,
            'classroom_id' => $classroom->id,
            'is_out_of_bounds' => false,
            'created_at' => now()->subDays(3),
        ]);

        // Old out of bounds session (should not be included)
        $oldSession = AttendanceSession::factory()->create([
            'lecturer_id' => $lecturer->id,
            'classroom_id' => $classroom->id,
            'is_out_of_bounds' => true,
            'created_at' => now()->subDays(35),
        ]);

        $result = $this->service->getOutOfBoundsSessions($department->id, 30);

        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('id', $outOfBoundsSession1->id));
        $this->assertTrue($result->contains('id', $outOfBoundsSession2->id));
        $this->assertFalse($result->contains('id', $normalSession->id));
        $this->assertFalse($result->contains('id', $oldSession->id));
    }

    public function test_calculates_compliance_rate()
    {
        $lecturer = Lecturer::factory()->create();

        // Create 10 sessions total
        AttendanceSession::factory()->count(7)->create([
            'lecturer_id' => $lecturer->id,
            'is_out_of_bounds' => false,
            'created_at' => now()->subDays(5),
        ]);

        AttendanceSession::factory()->count(3)->create([
            'lecturer_id' => $lecturer->id,
            'is_out_of_bounds' => true,
            'created_at' => now()->subDays(10),
        ]);

        $result = $this->service->calculateComplianceRate($lecturer->id, 30);

        $this->assertEquals(10, $result['total_sessions']);
        $this->assertEquals(7, $result['compliant_sessions']);
        $this->assertEquals(3, $result['out_of_bounds_sessions']);
        $this->assertEquals(70.0, $result['compliance_rate']);
    }

    public function test_calculates_compliance_rate_with_no_sessions()
    {
        $lecturer = Lecturer::factory()->create();

        $result = $this->service->calculateComplianceRate($lecturer->id, 30);

        $this->assertEquals(0, $result['total_sessions']);
        $this->assertEquals(0, $result['compliant_sessions']);
        $this->assertEquals(0, $result['out_of_bounds_sessions']);
        $this->assertEquals(100.0, $result['compliance_rate']);
    }

    public function test_haversine_formula_with_same_coordinates()
    {
        $distance = $this->service->calculateDistance(9.0820, 8.6753, 9.0820, 8.6753);
        $this->assertEquals(0.0, $distance);
    }

    public function test_haversine_formula_with_antipodal_points()
    {
        // Test with points on opposite sides of earth
        $distance = $this->service->calculateDistance(0, 0, 0, 180);
        
        // Should be approximately half the earth's circumference
        $expectedDistance = pi() * 6371; // π * radius
        $this->assertGreaterThan($expectedDistance * 0.99, $distance);
        $this->assertLessThan($expectedDistance * 1.01, $distance);
    }
}