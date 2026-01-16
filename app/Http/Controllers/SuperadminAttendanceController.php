<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use Carbon\Carbon;

class SuperadminAttendanceController extends Controller
{
    // Show all attendance attempts (present and denied) with location
    public function index(Request $request)
    {
        $attendances = Attendance::with(['student.user', 'student.department', 'attendanceSession', 'classroom.course', 'classroom.lecturer.user'])
            ->orderByDesc('captured_at')
            ->paginate(50);
        
        // Get comprehensive statistics for KPIs and charts
        $stats = $this->getComprehensiveStats();
        
        return view('superadmin.attendance_audit', compact('attendances', 'stats'));
    }

    /**
     * Get comprehensive statistics for dashboard
     */
    private function getComprehensiveStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $yesterday = Carbon::yesterday();
        
        return [
            'overall' => [
                'total_attempts' => Attendance::count(),
                'total_present' => Attendance::where('status', 'present')->count(),
                'total_denied' => Attendance::where('status', 'denied')->count(),
                'unique_students' => Attendance::distinct('student_id')->count('student_id'),
                'suspected_spoofers' => Attendance::select('student_id')
                    ->where('status', 'denied')
                    ->groupBy('student_id')
                    ->havingRaw('COUNT(*) >= 3')
                    ->get()->count(),
            ],
            'today' => [
                'attempts' => Attendance::whereDate('captured_at', $today)->count(),
                'present' => Attendance::whereDate('captured_at', $today)->where('status', 'present')->count(),
                'denied' => Attendance::whereDate('captured_at', $today)->where('status', 'denied')->count(),
                'sessions' => AttendanceSession::whereDate('created_at', $today)->count(),
                'active_sessions' => AttendanceSession::where('is_active', true)->count(),
            ],
            'this_week' => [
                'attempts' => Attendance::where('captured_at', '>=', $thisWeek)->count(),
                'present' => Attendance::where('captured_at', '>=', $thisWeek)->where('status', 'present')->count(),
                'denied' => Attendance::where('captured_at', '>=', $thisWeek)->where('status', 'denied')->count(),
            ],
            'this_month' => [
                'attempts' => Attendance::where('captured_at', '>=', $thisMonth)->count(),
                'present' => Attendance::where('captured_at', '>=', $thisMonth)->where('status', 'present')->count(),
                'denied' => Attendance::where('captured_at', '>=', $thisMonth)->where('status', 'denied')->count(),
            ],
            'yesterday' => [
                'attempts' => Attendance::whereDate('captured_at', $yesterday)->count(),
                'present' => Attendance::whereDate('captured_at', $yesterday)->where('status', 'present')->count(),
                'denied' => Attendance::whereDate('captured_at', $yesterday)->where('status', 'denied')->count(),
            ],
            'biometric' => [
                'total_biometric' => Attendance::whereNotNull('image_path')->count(),
                'total_manual' => Attendance::whereNull('image_path')->count(),
                'biometric_success_rate' => Attendance::whereNotNull('image_path')->count() > 0 
                    ? round((Attendance::whereNotNull('image_path')->where('status', 'present')->count() / Attendance::whereNotNull('image_path')->count()) * 100, 1)
                    : 0,
            ],
            'sessions' => [
                'total' => AttendanceSession::count(),
                'active' => AttendanceSession::where('is_active', true)->count(),
                'completed_today' => AttendanceSession::whereDate('created_at', $today)->where('is_active', false)->count(),
            ],
            'recent_activity' => Attendance::with(['student.user', 'classroom'])
                ->orderByDesc('captured_at')
                ->limit(20)
                ->get()
                ->map(function($a) {
                    return [
                        'id' => $a->id,
                        'student_name' => $a->student->user->full_name ?? 'Unknown',
                        'matric' => $a->student->matric_number ?? '-',
                        'class_name' => $a->classroom->class_name ?? '-',
                        'status' => $a->status,
                        'time' => $a->captured_at ? $a->captured_at->format('H:i:s') : '-',
                        'date' => $a->captured_at ? $a->captured_at->format('Y-m-d') : '-',
                        'method' => $a->image_path ? 'Biometric' : 'Manual',
                    ];
                }),
        ];
    }

    public function exportCsv(Request $request)
    {
        $attendances = \App\Models\Attendance::with(['student', 'attendanceSession', 'classroom'])
            ->orderByDesc('captured_at')
            ->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_audit.csv"',
        ];
        $callback = function() use ($attendances) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Student', 'Matric Number', 'Class', 'Course Code', 'Session Code', 'Time', 'Status', 'Latitude', 'Longitude', 'Spoofing Flag'
            ]);
            $deniedCounts = [];
            foreach ($attendances as $a) {
                $studentId = $a->student->id ?? null;
                if ($a->status === 'denied' && $studentId) {
                    $deniedCounts[$studentId] = ($deniedCounts[$studentId] ?? 0) + 1;
                }
            }
            foreach ($attendances as $a) {
                $studentId = $a->student->id ?? null;
                $spoofing = ($studentId && ($deniedCounts[$studentId] ?? 0) >= 3) ? 'FLAG' : '';
                fputcsv($handle, [
                    $a->student->full_name ?? '-',
                    $a->student->matric_number ?? '-',
                    $a->classroom->class_name ?? '-',
                    $a->classroom->course_code ?? '-',
                    $a->attendanceSession->code ?? '-',
                    $a->captured_at,
                    $a->status,
                    $a->latitude,
                    $a->longitude,
                    $spoofing
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function dashboardStats(Request $request)
    {
        $stats = $this->getComprehensiveStats();
        
        // Get hourly data for today
        $hourlyData = [];
        $today = Carbon::today();
        for ($i = 0; $i < 24; $i++) {
            $hour = $today->copy()->addHours($i);
            $hourEnd = $hour->copy()->addHour();
            $hourlyData[] = [
                'hour' => $hour->format('H:00'),
                'present' => Attendance::where('status', 'present')
                    ->whereBetween('captured_at', [$hour, $hourEnd])
                    ->count(),
                'denied' => Attendance::where('status', 'denied')
                    ->whereBetween('captured_at', [$hour, $hourEnd])
                    ->count(),
            ];
        }
        
        // Get daily data for last 30 days
        $dailyData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyData[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M d'),
                'present' => Attendance::whereDate('captured_at', $date)->where('status', 'present')->count(),
                'denied' => Attendance::whereDate('captured_at', $date)->where('status', 'denied')->count(),
                'total' => Attendance::whereDate('captured_at', $date)->count(),
            ];
        }
        
        // Get department breakdown
        $departmentBreakdown = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->join('departments', 'students.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('COUNT(*) as total'), 
                DB::raw('SUM(CASE WHEN attendances.status = "present" THEN 1 ELSE 0 END) as present'))
            ->groupBy('departments.id', 'departments.name')
            ->get();
        
        // Get status breakdown
        $statusBreakdown = [
            'present' => Attendance::where('status', 'present')->count(),
            'denied' => Attendance::where('status', 'denied')->count(),
        ];
        
        // Get method breakdown
        $methodBreakdown = [
            'biometric' => Attendance::whereNotNull('image_path')->count(),
            'manual' => Attendance::whereNull('image_path')->count(),
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'hourly_data' => $hourlyData,
            'daily_data' => $dailyData,
            'department_breakdown' => $departmentBreakdown,
            'status_breakdown' => $statusBreakdown,
            'method_breakdown' => $methodBreakdown,
        ]);
    }

    // API: Return attendance records as JSON for the dashboard
    public function apiIndex(Request $request)
    {
        $query = Attendance::with(['student', 'classroom']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('matric_number', 'like', "%$search%") ;
            });
        }
        if ($request->filled('class_id')) {
            $query->where('classroom_id', $request->class_id);
        }
        if ($request->filled('level')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('academic_level', $request->level);
            });
        }
        if ($request->filled('date')) {
            $query->whereDate('captured_at', $request->date);
        }
        $records = $query->latest('captured_at')->get()->map(function($a) {
            return [
                'id' => $a->id,
                'student_name' => $a->student ? $a->student->full_name : '',
                'matric_number' => $a->student ? $a->student->matric_number : '',
                'class_name' => $a->classroom ? $a->classroom->class_name : '',
                'class_id' => $a->classroom_id,
                'level' => $a->student ? $a->student->academic_level : '',
                'captured_at' => $a->captured_at ? $a->captured_at->format('Y-m-d H:i:s') : '',
                'status' => ucfirst($a->status ?? 'Present'),
                'method' => $a->image_path ? 'Biometric' : 'Manual',
            ];
        });
        return response()->json(['success' => true, 'data' => $records]);
    }

    // API: Return stats for KPI cards
    public function stats(Request $request)
    {
        return $this->dashboardStats($request);
    }

    /**
     * Get live activity feed
     */
    public function liveActivity(Request $request)
    {
        $limit = $request->get('limit', 50);
        
        $activities = Attendance::with(['student.user', 'student.department', 'classroom.course', 'attendanceSession'])
            ->orderByDesc('captured_at')
            ->limit($limit)
            ->get()
            ->map(function($a) {
                return [
                    'id' => $a->id,
                    'student_name' => $a->student->user->full_name ?? 'Unknown',
                    'matric' => $a->student->matric_number ?? '-',
                    'department' => $a->student->department->name ?? '-',
                    'class_name' => $a->classroom->class_name ?? '-',
                    'course' => $a->classroom->course->course_name ?? '-',
                    'session_code' => $a->attendanceSession->code ?? '-',
                    'status' => $a->status,
                    'method' => $a->image_path ? 'Biometric' : 'Manual',
                    'time' => $a->captured_at ? $a->captured_at->format('H:i:s') : '-',
                    'date' => $a->captured_at ? $a->captured_at->format('Y-m-d') : '-',
                    'timestamp' => $a->captured_at ? $a->captured_at->toIso8601String() : null,
                    'location' => $a->latitude && $a->longitude ? ['lat' => $a->latitude, 'lng' => $a->longitude] : null,
                ];
            });
        
        return response()->json([
            'success' => true,
            'activities' => $activities,
            'count' => $activities->count(),
        ]);
    }
} 