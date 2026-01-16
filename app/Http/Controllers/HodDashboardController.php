<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Hod;
use App\Models\Student;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\AuditLog;
use App\Services\HODDashboardService;
use App\Services\AttendanceCalculationService;

class HodDashboardController extends Controller
{
    private HODDashboardService $dashboardService;
    private AttendanceCalculationService $attendanceService;

    public function __construct(
        HODDashboardService $dashboardService,
        AttendanceCalculationService $attendanceService
    ) {
        $this->dashboardService = $dashboardService;
        $this->attendanceService = $attendanceService;
    }

    /**
     * Display the HOD dashboard view
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $hod = Auth::guard('hod')->user();
            
            if (!$hod) {
                return redirect()->route('hod.login')->with('error', 'Please log in to access the dashboard.');
            }

            // Log dashboard access for audit trail
            AuditLog::log('view', 'hod_dashboard', 'HOD accessed dashboard', $hod, null, null, null, 'low');

            // Get comprehensive dashboard data using the service
            $dashboardData = $this->dashboardService->getDepartmentOverview($hod->department_id);
            $thresholdCompliance = $this->dashboardService->getThresholdComplianceData($hod->department_id);
            $performanceMetrics = $this->dashboardService->getPerformanceMetrics($hod->department_id);
            $recentActivities = collect($this->dashboardService->getRecentActivity($hod->department_id));

            // Extract variables for the view
            $totalStudents = $dashboardData['total_students'] ?? 0;
            $totalLecturers = $dashboardData['total_lecturers'] ?? 0;
            $activeCourses = $dashboardData['total_classes'] ?? 0;
            $averageAttendance = $dashboardData['average_attendance'] ?? 0;
            $atRiskStudents = $thresholdCompliance['non_compliant_students'] ?? 0;
            
            // Mock exam stats for now - should be calculated from ExamEligibilityService
            $examStats = [
                'eligible' => 0,
                'ineligible' => 0
            ];

            $recentActivities = collect([]);

            return view('hod.dashboard', compact(
                'hod',
                'totalStudents',
                'totalLecturers',
                'activeCourses',
                'averageAttendance',
                'atRiskStudents',
                'examStats',
                'recentActivities'
            ));
        } catch (\Exception $e) {
            Log::error('HOD Dashboard Error: ' . $e->getMessage(), [
                'hod_id' => Auth::guard('hod')->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('hod.login')->with('error', 'An error occurred while loading the dashboard.');
        }
    }

    /**
     * Get dashboard statistics for API consumption
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $hod = Auth::guard('hod')->user();
            
            if (!$hod) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required'
                ], 401);
            }

            // Get comprehensive dashboard statistics using the service
            $dashboardData = $this->dashboardService->getDepartmentOverview($hod->department_id);
            $thresholdCompliance = $this->dashboardService->getThresholdComplianceData($hod->department_id);
            $performanceMetrics = $this->dashboardService->getPerformanceMetrics($hod->department_id);

            // Log API access
            AuditLog::log('api_access', 'dashboard_stats', 'HOD accessed dashboard stats API', $hod, null, null, null, 'low');

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => $dashboardData,
                    'threshold_compliance' => $thresholdCompliance,
                    'performance_metrics' => $performanceMetrics,
                ],
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard Stats API Error: ' . $e->getMessage(), [
                'hod_id' => Auth::guard('hod')->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'Failed to retrieve dashboard statistics'
            ], 500);
        }
    }

    /**
     * Get live staff activity for real-time dashboard updates
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLiveStaffActivity(Request $request)
    {
        try {
            $hod = Auth::guard('hod')->user();
            
            if (!$hod) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required'
                ], 401);
            }

            // Get active sessions and recent activities
            $activeSessions = $this->dashboardService->getActiveSessions($hod->department_id);
            $recentActivities = $this->dashboardService->getRecentActivity($hod->department_id, 15);

            return response()->json([
                'success' => true,
                'data' => [
                    'active_sessions' => $activeSessions,
                    'recent_activities' => $recentActivities,
                    'summary' => [
                        'total_active_sessions' => $activeSessions->count(),
                        'out_of_bounds_sessions' => $activeSessions->where('is_out_of_bounds', true)->count(),
                        'total_students_in_session' => $activeSessions->sum('total_students'),
                        'total_marked_present' => $activeSessions->sum('marked_present'),
                    ]
                ],
                'last_updated' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Live Staff Activity API Error: ' . $e->getMessage(), [
                'hod_id' => Auth::guard('hod')->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'Failed to retrieve live staff activity'
            ], 500);
        }
    }

    /**
     * Get attendance chart data for visualizations
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttendanceChart(Request $request)
    {
        try {
            $hod = Auth::guard('hod')->user();
            
            if (!$hod) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required'
                ], 401);
            }

            // Validate request parameters
            $groupBy = $request->get('group_by', 'level'); // level, course, staff, monthly
            $days = (int) $request->get('days', 30);
            
            // Validate groupBy parameter
            if (!in_array($groupBy, ['level', 'course', 'staff', 'monthly'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid parameter',
                    'message' => 'group_by must be one of: level, course, staff, monthly'
                ], 400);
            }

            // Validate days parameter
            if ($days < 1 || $days > 365) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid parameter',
                    'message' => 'days must be between 1 and 365'
                ], 400);
            }

            // Get chart data using the service
            $chartData = $this->dashboardService->getAttendanceChartData($hod->department_id, $groupBy, $days);

            return response()->json([
                'success' => true,
                'data' => $chartData,
                'parameters' => [
                    'group_by' => $groupBy,
                    'days' => $days,
                    'department_id' => $hod->department_id,
                ],
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Attendance Chart API Error: ' . $e->getMessage(), [
                'hod_id' => Auth::guard('hod')->id(),
                'group_by' => $request->get('group_by'),
                'days' => $request->get('days'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'Failed to retrieve attendance chart data'
            ], 500);
        }
    }

    /**
     * Alias for getLiveStaffActivity to maintain backward compatibility with existing routes
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLiveActivity(Request $request)
    {
        return $this->getLiveStaffActivity($request);
    }

    /**
     * Global search functionality
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $hod = Auth::guard('hod')->user();
        $results = [];

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        // Search students
        $students = Student::where('department_id', $hod->department_id)
            ->where(function($q) use ($query) {
                $q->where('matric_number', 'like', "%{$query}%")
                  ->orWhere('full_name', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        foreach ($students as $student) {
            $results[] = [
                'type' => 'student',
                'title' => $student->full_name,
                'subtitle' => $student->matric_number,
                'url' => route('hod.management.students.show', $student->id)
            ];
        }

        // Search courses
        $courses = Course::whereHas('classrooms', function($q) use ($hod) {
            $q->whereHas('lecturer', function($lecturerQuery) use ($hod) {
                $lecturerQuery->where('department_id', $hod->department_id);
            });
        })
        ->where(function($q) use ($query) {
            $q->where('code', 'like', "%{$query}%")
              ->orWhere('name', 'like', "%{$query}%");
        })
        ->limit(5)
        ->get();

        foreach ($courses as $course) {
            $results[] = [
                'type' => 'course',
                'title' => $course->code . ' - ' . $course->name,
                'subtitle' => 'Course',
                'url' => route('hod.monitoring.courses') . '?course_id=' . $course->id
            ];
        }

        // Search lecturers
        $lecturers = Lecturer::where('department_id', $hod->department_id)
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        foreach ($lecturers as $lecturer) {
            $results[] = [
                'type' => 'lecturer',
                'title' => $lecturer->name,
                'subtitle' => $lecturer->email ?? 'Lecturer',
                'url' => route('hod.management.lecturers.show', $lecturer->id)
            ];
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Get notifications for HOD
     */
    public function getNotifications(Request $request)
    {
        try {
            $hod = Auth::guard('hod')->user();
            
            if (!$hod) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required'
                ], 401);
            }

            // Get recent audit logs as notifications - simplified query
            $logs = AuditLog::where(function($q) use ($hod) {
                    $q->where('user_type', 'hod')
                      ->where('user_id', $hod->id)
                      ->orWhere(function($subQ) use ($hod) {
                          // Get logs related to department students
                          $subQ->where('model_type', 'App\Models\Student')
                               ->whereIn('model_id', function($studentQuery) use ($hod) {
                                   $studentQuery->select('id')
                                                ->from('students')
                                                ->where('department_id', $hod->department_id);
                               });
                      });
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $notifications = [];
            foreach ($logs as $log) {
                try {
                    $notifications[] = [
                        'id' => $log->id,
                        'type' => $log->status === 'error' ? 'alert' : ($log->priority === 'high' ? 'warning' : 'info'),
                        'title' => ucfirst(str_replace('_', ' ', $log->action)),
                        'message' => $log->description ?? $log->action ?? 'Notification',
                        'time_ago' => $log->created_at ? $log->created_at->diffForHumans() : 'Recently',
                        'read' => false
                    ];
                } catch (\Exception $e) {
                    // Skip problematic log entries
                    continue;
                }
            }

            // Check for at-risk students - simplified query
            try {
                $atRiskCount = Student::where('department_id', $hod->department_id)
                    ->where('is_active', true)
                    ->count(); // Simplified - can be enhanced later with actual attendance calculations
                
                // For now, return a placeholder count (can be calculated properly later)
                $atRiskCount = 0; // Placeholder until proper calculation is implemented
            } catch (\Exception $e) {
                Log::warning('Error calculating at-risk students count', ['error' => $e->getMessage()]);
                $atRiskCount = 0;
            }

            if ($atRiskCount > 0) {
                array_unshift($notifications, [
                    'id' => 'at_risk_' . time(),
                    'type' => 'alert',
                    'title' => 'At-Risk Students',
                    'message' => "{$atRiskCount} students have attendance below 75%",
                    'time_ago' => 'Just now',
                    'read' => false
                ]);
            }

            $unreadCount = collect($notifications)->where('read', false)->count();

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            Log::error('HOD Notifications API Error: ' . $e->getMessage(), [
                'hod_id' => Auth::guard('hod')->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'notifications' => [],
                'unread_count' => 0,
                'error' => 'Failed to load notifications'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Request $request)
    {
        // In a real implementation, you'd have a notifications table
        // For now, we'll just return success
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(Request $request)
    {
        // In a real implementation, you'd mark all as read in database
        return response()->json(['success' => true]);
    }
}