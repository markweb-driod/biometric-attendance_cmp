<?php

namespace App\Services;

use App\Models\AttendanceSession;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    /**
     * Earth's radius in kilometers
     */
    private const EARTH_RADIUS_KM = 6371;

    /**
     * Calculate distance between two points using Haversine formula
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in kilometers
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // Convert degrees to radians
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);

        // Haversine formula
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_KM * $c;
    }

    /**
     * Verify if a student is within the required venue location
     *
     * @param float $studentLat
     * @param float $studentLon
     * @param float $venueLat
     * @param float $venueLon
     * @param float $radiusKm
     * @return bool
     */
    public function verifyStudentLocation(float $studentLat, float $studentLon, float $venueLat, float $venueLon, float $radiusKm): bool
    {
        $distance = $this->calculateDistance($studentLat, $studentLon, $venueLat, $venueLon);
        return $distance <= $radiusKm;
    }

    /**
     * Flag an attendance session as out of bounds and notify HOD
     *
     * @param AttendanceSession $session
     * @param float $actualDistance
     * @param float $allowedRadius
     * @return void
     */
    public function flagOutOfBoundsSession(AttendanceSession $session, float $actualDistance, float $allowedRadius): void
    {
        try {
            // Update session with out of bounds flag
            $session->update([
                'is_out_of_bounds' => true,
                'distance_from_classroom' => $actualDistance,
                'flagged_at' => now(),
            ]);

            // Log the incident
            $this->logOutOfBoundsIncident($session, $actualDistance, $allowedRadius);

            // Create audit log entry
            AuditLog::create([
                'user_id' => $session->lecturer_id,
                'user_type' => 'App\Models\Lecturer',
                'role' => 'lecturer',
                'action' => 'out_of_bounds_session',
                'description' => "Attendance session conducted outside geofence. Distance: {$actualDistance}km, Allowed: {$allowedRadius}km",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'flagged',
                'metadata' => [
                    'session_id' => $session->id,
                    'classroom_id' => $session->classroom_id,
                    'actual_distance' => $actualDistance,
                    'allowed_radius' => $allowedRadius,
                    'lecturer_location' => [
                        'latitude' => $session->lecturer_latitude,
                        'longitude' => $session->lecturer_longitude,
                    ],
                    'classroom_location' => [
                        'latitude' => $session->classroom->latitude ?? null,
                        'longitude' => $session->classroom->longitude ?? null,
                    ],
                ],
            ]);

            Log::warning('Out of bounds attendance session detected', [
                'session_id' => $session->id,
                'lecturer_id' => $session->lecturer_id,
                'distance' => $actualDistance,
                'allowed_radius' => $allowedRadius,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to flag out of bounds session', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Validate attendance session location
     *
     * @param AttendanceSession $session
     * @param float $geofenceRadius
     * @return array
     */
    public function validateSessionLocation(AttendanceSession $session, float $geofenceRadius = 0.5): array
    {
        // Check if we have required location data
        if (!$session->lecturer_latitude || !$session->lecturer_longitude) {
            return [
                'is_valid' => false,
                'reason' => 'Missing lecturer location data',
                'distance' => null,
            ];
        }

        if (!$session->classroom->latitude || !$session->classroom->longitude) {
            return [
                'is_valid' => false,
                'reason' => 'Missing classroom location data',
                'distance' => null,
            ];
        }

        $distance = $this->calculateDistance(
            $session->classroom->latitude,
            $session->classroom->longitude,
            $session->lecturer_latitude,
            $session->lecturer_longitude
        );

        $isValid = $distance <= $geofenceRadius;

        if (!$isValid) {
            $this->flagOutOfBoundsSession($session, $distance, $geofenceRadius);
        }

        return [
            'is_valid' => $isValid,
            'distance' => round($distance, 3),
            'allowed_radius' => $geofenceRadius,
            'reason' => $isValid ? 'Within geofence' : 'Outside geofence',
        ];
    }

    /**
     * Get sessions that are out of bounds for a department
     *
     * @param int $departmentId
     * @param int $days Number of days to look back
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOutOfBoundsSessions(int $departmentId, int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return AttendanceSession::whereHas('classroom.lecturer', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->where('is_out_of_bounds', true)
        ->where('created_at', '>=', now()->subDays($days))
        ->with(['classroom', 'lecturer'])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Calculate geofence compliance rate for a lecturer
     *
     * @param int $lecturerId
     * @param int $days
     * @return array
     */
    public function calculateComplianceRate(int $lecturerId, int $days = 30): array
    {
        $totalSessions = AttendanceSession::where('lecturer_id', $lecturerId)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

        $outOfBoundsSessions = AttendanceSession::where('lecturer_id', $lecturerId)
            ->where('is_out_of_bounds', true)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

        $complianceRate = $totalSessions > 0 ? (($totalSessions - $outOfBoundsSessions) / $totalSessions) * 100 : 100;

        return [
            'total_sessions' => $totalSessions,
            'compliant_sessions' => $totalSessions - $outOfBoundsSessions,
            'out_of_bounds_sessions' => $outOfBoundsSessions,
            'compliance_rate' => round($complianceRate, 2),
        ];
    }

    /**
     * Log out of bounds incident
     *
     * @param AttendanceSession $session
     * @param float $actualDistance
     * @param float $allowedRadius
     * @return void
     */
    private function logOutOfBoundsIncident(AttendanceSession $session, float $actualDistance, float $allowedRadius): void
    {
        Log::channel('security')->warning('Geofence violation detected', [
            'session_id' => $session->id,
            'lecturer_id' => $session->lecturer_id,
            'classroom_id' => $session->classroom_id,
            'actual_distance_km' => $actualDistance,
            'allowed_radius_km' => $allowedRadius,
            'violation_severity' => $this->getViolationSeverity($actualDistance, $allowedRadius),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine violation severity based on distance
     *
     * @param float $actualDistance
     * @param float $allowedRadius
     * @return string
     */
    private function getViolationSeverity(float $actualDistance, float $allowedRadius): string
    {
        $excessDistance = $actualDistance - $allowedRadius;
        
        if ($excessDistance <= 0.1) {
            return 'minor';
        } elseif ($excessDistance <= 0.5) {
            return 'moderate';
        } elseif ($excessDistance <= 1.0) {
            return 'major';
        } else {
            return 'severe';
        }
    }
}