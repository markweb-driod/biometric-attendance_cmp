<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSession;
use App\Services\SessionMonitoringService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionMonitoringController extends Controller
{
    protected $sessionMonitoringService;

    public function __construct(SessionMonitoringService $sessionMonitoringService)
    {
        $this->middleware('auth:superadmin');
        $this->sessionMonitoringService = $sessionMonitoringService;
    }

    /**
     * Display the session monitoring dashboard
     */
    public function index()
    {
        $stats = [
            'active_sessions' => $this->sessionMonitoringService->getActiveSessionsCount(),
            'active_superadmin' => $this->sessionMonitoringService->getActiveSessionsCount('Superadmin'),
            'active_lecturers' => $this->sessionMonitoringService->getActiveSessionsCount('Lecturer'),
            'active_hods' => $this->sessionMonitoringService->getActiveSessionsCount('Hod'),
            'recent_stats' => $this->sessionMonitoringService->getRecentStats(7),
        ];

        return view('superadmin.session-monitoring.index', compact('stats'));
    }

    /**
     * Get live active sessions (AJAX endpoint)
     */
    public function liveSessions(Request $request)
    {
        $query = UserSession::active()
            ->orderBy('login_at', 'desc');

        // Filters
        if ($request->has('user_type') && $request->user_type) {
            $query->byUserType($request->user_type);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $sessions = $query->paginate(20);

        // Format sessions for display
        $formatted = $sessions->map(function($session) {
            return [
                'id' => $session->id,
                'session_id' => substr($session->session_id, 0, 20) . '...',
                'user_type' => $session->user_type,
                'user_name' => $session->full_name,
                'identifier' => $session->identifier,
                'department' => $session->department_name,
                'login_at' => $session->login_at->format('M j, Y g:i A'),
                'last_activity' => $session->last_activity_at ? $session->last_activity_at->diffForHumans() : 'N/A',
                'duration' => $session->duration,
                'ip_address' => $session->ip_address,
                'device' => $session->device_type,
                'browser' => $session->browser,
                'os' => $session->os,
                'location' => $session->city && $session->country ? "{$session->city}, {$session->country}" : ($session->country ?? 'Unknown'),
            ];
        });

        return response()->json([
            'sessions' => $formatted,
            'pagination' => [
                'current_page' => $sessions->currentPage(),
                'last_page' => $sessions->lastPage(),
                'total' => $sessions->total(),
            ],
        ]);
    }

    /**
     * Get session history (AJAX endpoint)
     */
    public function sessionHistory(Request $request)
    {
        $query = UserSession::query()
            ->orderBy('login_at', 'desc');

        // Filters
        if ($request->has('user_type') && $request->user_type) {
            $query->byUserType($request->user_type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('login_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('login_at', '<=', $request->date_to);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $sessions = $query->paginate(25);

        // Format sessions for display
        $formatted = $sessions->map(function($session) {
            return [
                'id' => $session->id,
                'session_id' => substr($session->session_id, 0, 20) . '...',
                'user_type' => $session->user_type,
                'user_name' => $session->full_name,
                'identifier' => $session->identifier,
                'department' => $session->department_name,
                'status' => $session->status,
                'login_at' => $session->login_at->format('M j, Y g:i A'),
                'logout_at' => $session->logout_at ? $session->logout_at->format('M j, Y g:i A') : null,
                'duration' => $session->duration,
                'ip_address' => $session->ip_address,
                'device' => $session->device_type,
                'browser' => $session->browser,
                'os' => $session->os,
                'location' => $session->city && $session->country ? "{$session->city}, {$session->country}" : ($session->country ?? 'Unknown'),
                'is_terminated' => $session->status === 'terminated',
                'terminated_by' => $session->terminated_by,
                'termination_reason' => $session->termination_reason,
            ];
        });

        return response()->json([
            'sessions' => $formatted,
            'pagination' => [
                'current_page' => $sessions->currentPage(),
                'last_page' => $sessions->lastPage(),
                'total' => $sessions->total(),
            ],
        ]);
    }

    /**
     * Get detailed session information
     */
    public function sessionDetails($id)
    {
        $session = UserSession::findOrFail($id);

        return response()->json([
            'session' => [
                'id' => $session->id,
                'session_id' => $session->session_id,
                'user_type' => $session->user_type,
                'user_name' => $session->full_name,
                'identifier' => $session->identifier,
                'department' => $session->department_name,
                'status' => $session->status,
                'login_at' => $session->login_at->format('F j, Y g:i:s A'),
                'logout_at' => $session->logout_at ? $session->logout_at->format('F j, Y g:i:s A') : null,
                'last_activity_at' => $session->last_activity_at ? $session->last_activity_at->format('F j, Y g:i:s A') : null,
                'duration' => $session->duration,
                'duration_seconds' => $session->duration_in_seconds,
                'ip_address' => $session->ip_address,
                'user_agent' => $session->user_agent,
                'device_type' => $session->device_type,
                'browser' => $session->browser,
                'os' => $session->os,
                'country' => $session->country,
                'city' => $session->city,
                'timezone' => $session->timezone,
                'activity_trail' => $session->activity_trail,
                'is_terminated' => $session->status === 'terminated',
                'terminated_by' => $session->terminated_by,
                'terminated_at' => $session->terminated_at ? $session->terminated_at->format('F j, Y g:i:s A') : null,
                'termination_reason' => $session->termination_reason,
            ],
        ]);
    }

    /**
     * Terminate a session forcefully
     */
    public function terminateSession(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $success = $this->sessionMonitoringService->terminateSession(
                $id,
                Auth::guard('superadmin')->id(),
                $request->input('reason')
            );

            if ($success) {
                Log::info('Session terminated by superadmin', [
                    'session_id' => $id,
                    'terminated_by' => Auth::guard('superadmin')->id(),
                    'reason' => $request->input('reason'),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Session terminated successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to terminate session',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to terminate session', [
                'session_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while terminating the session',
            ], 500);
        }
    }

    /**
     * Get session statistics
     */
    public function statistics(Request $request)
    {
        $days = $request->input('days', 7);
        
        $stats = $this->sessionMonitoringService->getRecentStats($days);

        // Additional statistics
        $stats['total_unique_users'] = UserSession::where('login_at', '>=', now()->subDays($days))
            ->distinct('user_type', 'user_id')
            ->count();

        $stats['avg_session_duration'] = UserSession::where('login_at', '>=', now()->subDays($days))
            ->whereNotNull('logout_at')
            ->get()
            ->avg(function($session) {
                return $session->duration_in_seconds;
            });

        return response()->json($stats);
    }
}
