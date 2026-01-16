<?php

namespace App\Services;

use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SessionMonitoringService
{
    /**
     * Create a new session record on login
     */
    public function createSession($user, string $userType): ?UserSession
    {
        try {
            // Parse user agent to extract device info
            $userAgentInfo = $this->parseUserAgent(request()->userAgent());

            // Get department info for lecturer/hod
            $departmentId = null;
            $departmentName = null;
            $fullName = null;
            $identifier = null;

            if ($userType === 'Superadmin') {
                $fullName = $user->full_name;
                $identifier = $user->email;
            } elseif ($userType === 'Lecturer') {
                $fullName = $user->user ? $user->user->full_name : 'Unknown';
                $identifier = $user->staff_id;
                $departmentId = $user->department_id;
                $departmentName = $user->department ? $user->department->name : null;
            } elseif ($userType === 'Hod') {
                $fullName = $user->user ? $user->user->full_name : 'Unknown';
                $identifier = $user->staff_id;
                $departmentId = $user->department_id;
                $departmentName = $user->department ? $user->department->name : null;
            }

            $session = UserSession::create([
                'session_id' => session()->getId(),
                'user_type' => $userType,
                'user_id' => $user->id,
                'identifier' => $identifier,
                'full_name' => $fullName,
                'login_at' => now(),
                'last_activity_at' => now(),
                'status' => 'active',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_type' => $userAgentInfo['device_type'],
                'browser' => $userAgentInfo['browser'],
                'os' => $userAgentInfo['os'],
                'department_id' => $departmentId,
                'department_name' => $departmentName,
                'activity_trail' => [[
                    'action' => 'login',
                    'timestamp' => now()->toISOString(),
                    'metadata' => [],
                ]],
            ]);

            Log::info('Session created', [
                'session_id' => $session->session_id,
                'user_type' => $userType,
                'user_id' => $user->id,
            ]);

            return $session;
        } catch (\Exception $e) {
            Log::error('Failed to create session', [
                'error' => $e->getMessage(),
                'user_type' => $userType,
            ]);
            return null;
        }
    }

    /**
     * Update session activity
     */
    public function updateActivity(string $sessionId): bool
    {
        try {
            $session = UserSession::where('session_id', $sessionId)
                ->where('status', 'active')
                ->first();

            if ($session) {
                $session->updateActivity();
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to update session activity', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);
            return false;
        }
    }

    /**
     * End session on logout
     */
    public function endSession(string $sessionId): bool
    {
        try {
            // First try to find by session_id
            $session = UserSession::where('session_id', $sessionId)
                ->where('status', 'active')
                ->first();

            // If not found, try to find the most recent active session for the authenticated user
            // This handles cases where session was regenerated
            if (!$session) {
                // Try superadmin guard
                if (Auth::guard('superadmin')->check()) {
                    $user = Auth::guard('superadmin')->user();
                    $session = UserSession::where('user_type', 'Superadmin')
                        ->where('user_id', $user->id)
                        ->where('status', 'active')
                        ->orderBy('login_at', 'desc')
                        ->first();
                }
                // Try lecturer guard
                elseif (Auth::guard('lecturer')->check()) {
                    $user = Auth::guard('lecturer')->user();
                    $session = UserSession::where('user_type', 'Lecturer')
                        ->where('user_id', $user->id)
                        ->where('status', 'active')
                        ->orderBy('login_at', 'desc')
                        ->first();
                }
                // Try HOD guard
                elseif (Auth::guard('hod')->check()) {
                    $user = Auth::guard('hod')->user();
                    $session = UserSession::where('user_type', 'Hod')
                        ->where('user_id', $user->id)
                        ->where('status', 'active')
                        ->orderBy('login_at', 'desc')
                        ->first();
                }
            }

            if ($session) {
                $session->addActivity('logout');
                $session->markAsEnded();

                Log::info('Session ended', [
                    'session_id' => $sessionId,
                    'found_session_id' => $session->session_id,
                    'duration' => $session->duration,
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to end session', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);
            return false;
        }
    }

    /**
     * Terminate session forcefully
     */
    public function terminateSession(int $sessionId, int $terminatedBy, ?string $reason = null): bool
    {
        try {
            $session = UserSession::find($sessionId);

            if ($session) {
                $session->terminate($terminatedBy, $reason);

                // Also invalidate the actual Laravel session
                $laravelSession = DB::table('sessions')
                    ->where('id', $session->session_id)
                    ->first();

                if ($laravelSession) {
                    DB::table('sessions')->where('id', $session->session_id)->delete();
                }

                Log::info('Session terminated', [
                    'session_id' => $session->session_id,
                    'terminated_by' => $terminatedBy,
                    'reason' => $reason,
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to terminate session', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);
            return false;
        }
    }

    /**
     * Get active sessions count
     */
    public function getActiveSessionsCount(?string $userType = null): int
    {
        $query = UserSession::active();

        if ($userType) {
            $query->byUserType($userType);
        }

        return $query->count();
    }

    /**
     * Get recent sessions statistics
     */
    public function getRecentStats(int $days = 7): array
    {
        $startDate = now()->subDays($days);

        // Count unique users (compatible with SQLite)
        $uniqueUsers = UserSession::where('login_at', '>=', $startDate)
            ->selectRaw('COUNT(DISTINCT user_type || "-" || user_id) as count')
            ->first()->count ?? 0;
        
        return [
            'total_sessions' => UserSession::where('login_at', '>=', $startDate)->count(),
            'unique_users' => (int) $uniqueUsers,
            'superadmin_sessions' => UserSession::where('login_at', '>=', $startDate)
                ->byUserType('Superadmin')
                ->count(),
            'lecturer_sessions' => UserSession::where('login_at', '>=', $startDate)
                ->byUserType('Lecturer')
                ->count(),
            'hod_sessions' => UserSession::where('login_at', '>=', $startDate)
                ->byUserType('Hod')
                ->count(),
        ];
    }

    /**
     * Parse user agent string to extract device information
     */
    private function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'device_type' => 'unknown',
                'browser' => 'unknown',
                'os' => 'unknown',
            ];
        }

        $deviceType = 'desktop';
        $browser = 'Unknown Browser';
        $os = 'Unknown OS';

        // Detect OS
        if (preg_match('/Windows NT 10.0/i', $userAgent)) {
            $os = 'Windows 10/11';
        } elseif (preg_match('/Windows NT 6.3/i', $userAgent)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/Windows NT 6.2/i', $userAgent)) {
            $os = 'Windows 8';
        } elseif (preg_match('/Windows NT 6.1/i', $userAgent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/Mac OS X/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android';
            $deviceType = 'mobile';
        } elseif (preg_match('/iPhone|iPad/i', $userAgent)) {
            $os = 'iOS';
            $deviceType = preg_match('/iPhone/i', $userAgent) ? 'mobile' : 'tablet';
        }

        // Detect Browser
        if (preg_match('/Chrome/i', $userAgent) && !preg_match('/Edg/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Opera/i', $userAgent)) {
            $browser = 'Opera';
        }

        // Detect mobile/tablet more accurately
        if (preg_match('/Mobile|Android/i', $userAgent)) {
            $deviceType = 'mobile';
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            $deviceType = 'tablet';
        }

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
        ];
    }

    /**
     * Clean up expired sessions
     */
    public function cleanupExpiredSessions(int $timeoutMinutes = 120): int
    {
        $cutoffTime = now()->subMinutes($timeoutMinutes);
        
        $expired = UserSession::active()
            ->where('last_activity_at', '<', $cutoffTime)
            ->get();

        $count = 0;
        foreach ($expired as $session) {
            $session->markAsExpired();
            $count++;
        }

        Log::info('Expired sessions cleaned up', ['count' => $count]);

        return $count;
    }
}

