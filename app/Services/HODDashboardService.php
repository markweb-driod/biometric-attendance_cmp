<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Classroom;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\AcademicLevel;
use App\Services\AttendanceCalculationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class HODDashboardService
{
    private AttendanceCalculationService $attendanceService;

    public function __construct(AttendanceCalculationService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Get comprehensive department overview statistics
     *
     * @param int $departmentId
     * @return array
     */
    public function getDepartmentOverview(int $departmentId): array
    {
        $cacheKey = "hod_dashboard_overview_{$departmentId}";
        
        return Cache::remember($cacheKey, 300, function () use ($departmentId) {
            $totalStudents = Student::where('department_id', $departmentId)->where('is_active', true)->count();
            $totalLecturers = Lecturer::where('department_id', $departmentId)->where('is_active', true)->count();
            
            $totalClasses = Classroom::whereHas('lecturer', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId)->where('is_active', true);
            })->where('is_active', true)->count();

            $attendanceSummary = $this->attendanceService->getDepartmentAttendanceSummary($departmentId);
            $activeSessions = $this->getActiveSessions($departmentId);

            return [
                'total_students' => $totalStudents,
                'total_lecturers' => $totalLecturers,
                'total_classes' => $totalClasses,
                'average_attendance' => $attendanceSummary['average_attendance'],
                'active_sessions' => $activeSessions->count(),
                'attendance_distribution' => [
                    'excellent' => $attendanceSummary['excellent_count'],
                    'good' => $attendanceSummary['good_count'],
                    'warning' => $attendanceSummary['warning_count'],
                    'critical' => $attendanceSummary['critical_count'],
                ],
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get attendance chart data for visualizations
     *
     * @param int $departmentId
     * @param string $groupBy (level, course, staff, monthly)
     * @param int $days
     * @return array
     */
    public function getAttendanceChartData(int $departmentId, string $groupBy = 'level', int $days = 30): array
    {
        $cacheKey = "hod_attendance_chart_{$departmentId}_{$groupBy}_{$days}";
        
        return Cache::remember($cacheKey, 600, function () use ($departmentId, $groupBy, $days) {
            switch ($groupBy) {
                case 'level':
                    return $this->getAttendanceByLevel($departmentId, $days);
                case 'course':
                    return $this->getAttendanceByCourse($departmentId, $days);
                case 'staff':
                    return $this->getAttendanceByStaff($departmentId, $days);
                case 'monthly':
                    return $this->getMonthlyAttendanceTrend($departmentId);
                default:
                    return $this->getAttendanceByLevel($departmentId, $days);
            }
        });
    }

    /**
     * Get threshold compliance data
     *
     * @param int $departmentId
     * @param float $threshold
     * @return array
     */
    public function getThresholdComplianceData(int $departmentId, float $threshold = 75.0): array
    {
        $cacheKey = "hod_threshold_compliance_{$departmentId}_{$threshold}";
        
        return Cache::remember($cacheKey, 300, function () use ($departmentId, $threshold) {
            $belowThreshold = $this->attendanceService->getStudentsBelowThreshold($departmentId, $threshold);
            $totalStudents = Student::where('department_id', $departmentId)->where('is_active', true)->count();
            
            $complianceRate = $totalStudents > 0 
                ? (($totalStudents - $belowThreshold->count()) / $totalStudents) * 100 
                : 100;

            return [
                'total_students' => $totalStudents,
                'compliant_students' => $totalStudents - $belowThreshold->count(),
                'non_compliant_students' => $belowThreshold->count(),
                'compliance_rate' => round($complianceRate, 2),
                'threshold' => $threshold,
                'students_below_threshold' => $belowThreshold->take(10)->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'matric_number' => $student->matric_number,
                        'full_name' => $student->full_name,
                        'attendance_percentage' => $student->attendance_data['percentage'],
                        'status' => $student->attendance_data['status'],
                    ];
                })->toArray(),
            ];
        });
    }

    /**
     * Get currently active attendance sessions
     *
     * @param int $departmentId
     * @return Collection
     */
    public function getActiveSessions(int $departmentId): Collection
    {
        return AttendanceSession::whereHas('classroom.lecturer', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->whereNull('end_time')
        ->where('created_at', '>=', now()->subHours(6)) // Sessions within last 6 hours
        ->with([
            'classroom.course',
            'classroom.lecturer.user',
            'classroom.students'
        ])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($session) {
            return [
                'id' => $session->id,
                'course_name' => $session->classroom->course->name ?? 'N/A',
                'course_code' => $session->classroom->course->code ?? 'N/A',
                'lecturer_name' => $session->classroom->lecturer->user->name ?? 'N/A',
                'start_time' => $session->created_at,
                'duration_minutes' => $session->created_at->diffInMinutes(now()),
                'total_students' => $session->classroom->students->count(),
                'marked_present' => $session->attendances->count(),
                'location' => [
                    'latitude' => $session->lecturer_latitude,
                    'longitude' => $session->lecturer_longitude,
                ],
                'is_out_of_bounds' => $session->is_out_of_bounds ?? false,
            ];
        });
    }

    /**
     * Get recent activity feed for the dashboard
     *
     * @param int $departmentId
     * @param int $limit
     * @return array
     */
    public function getRecentActivity(int $departmentId, int $limit = 10): array
    {
        $recentSessions = AttendanceSession::whereHas('classroom.lecturer', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->with([
            'classroom.course',
            'classroom.lecturer.user'
        ])
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get()
        ->map(function ($session) {
            return [
                'type' => 'attendance_session',
                'title' => 'Attendance Session Started',
                'description' => "{$session->classroom->lecturer->user->name} started attendance for {$session->classroom->course->name}",
                'timestamp' => $session->created_at,
                'lecturer_name' => $session->classroom->lecturer->user->name,
                'course_name' => $session->classroom->course->name,
                'is_completed' => !is_null($session->end_time),
                'is_out_of_bounds' => $session->is_out_of_bounds ?? false,
            ];
        });

        return $recentSessions->map(function ($session) {
            return [
                'type' => 'attendance_session',
                'course' => $session['course_name'] ?? 'Unknown Course',
                'lecturer' => $session['lecturer_name'] ?? 'Unknown Lecturer',
                'session_name' => 'Attendance Session',
                'status' => $session['is_completed'] ? 'completed' : 'active',
                'time' => $session['timestamp']->diffForHumans(),
            ];
        })->toArray();
    }

    /**
     * Get performance metrics for the department
     *
     * @param int $departmentId
     * @param int $days
     * @return array
     */
    public function getPerformanceMetrics(int $departmentId, int $days = 30): array
    {
        $cacheKey = "hod_performance_metrics_{$departmentId}_{$days}";
        
        return Cache::remember($cacheKey, 900, function () use ($departmentId, $days) {
            $startDate = now()->subDays($days);
            
            // Total sessions conducted
            $totalSessions = AttendanceSession::whereHas('classroom.lecturer', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->where('created_at', '>=', $startDate)
            ->count();

            // Average session duration - Use database-agnostic approach
            $avgDuration = AttendanceSession::whereHas('classroom.lecturer', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('end_time')
            ->get()
            ->map(function ($session) {
                $start = \Carbon\Carbon::parse($session->created_at);
                $end = \Carbon\Carbon::parse($session->end_time);
                return $start->diffInMinutes($end);
            })
            ->filter()
            ->avg();

            // Punctuality rate (sessions started within 15 minutes of scheduled time)
            $punctualSessions = AttendanceSession::whereHas('classroom.lecturer', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->where('created_at', '>=', $startDate)
            ->where('is_punctual', true)
            ->count();

            $punctualityRate = $totalSessions > 0 ? ($punctualSessions / $totalSessions) * 100 : 0;

            // Geofence compliance
            $outOfBoundsSessions = AttendanceSession::whereHas('classroom.lecturer', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->where('created_at', '>=', $startDate)
            ->where('is_out_of_bounds', true)
            ->count();

            $geofenceCompliance = $totalSessions > 0 ? (($totalSessions - $outOfBoundsSessions) / $totalSessions) * 100 : 100;

            return [
                'total_sessions' => $totalSessions,
                'average_duration_minutes' => round($avgDuration ?? 0, 1),
                'punctuality_rate' => round($punctualityRate, 2),
                'geofence_compliance' => round($geofenceCompliance, 2),
                'out_of_bounds_sessions' => $outOfBoundsSessions,
                'period_days' => $days,
                'start_date' => $startDate->toDateString(),
                'end_date' => now()->toDateString(),
            ];
        });
    }

    /**
     * Get attendance data grouped by academic level
     *
     * @param int $departmentId
     * @param int $days
     * @return array
     */
    private function getAttendanceByLevel(int $departmentId, int $days): array
    {
        $levels = AcademicLevel::whereHas('students', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->get();

        $data = [];
        foreach ($levels as $level) {
            $students = Student::where('department_id', $departmentId)
                ->where('academic_level_id', $level->id)
                ->where('is_active', true)
                ->get();

            if ($students->count() > 0) {
                $attendanceData = $this->attendanceService->calculateBulkAttendance($students);
                $averageAttendance = collect($attendanceData)->avg('percentage');

                $data[] = [
                    'label' => $level->name,
                    'value' => round($averageAttendance, 2),
                    'student_count' => $students->count(),
                ];
            }
        }

        return [
            'type' => 'bar',
            'title' => 'Attendance by Academic Level',
            'data' => $data,
        ];
    }

    /**
     * Get attendance data grouped by course
     *
     * @param int $departmentId
     * @param int $days
     * @return array
     */
    private function getAttendanceByCourse(int $departmentId, int $days): array
    {
        $courses = Course::whereHas('classrooms.lecturer', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->get();

        $data = [];
        foreach ($courses as $course) {
            $students = Student::whereHas('classrooms', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })->where('department_id', $departmentId)->where('is_active', true)->get();

            if ($students->count() > 0) {
                $attendanceData = $this->attendanceService->calculateBulkAttendance($students, $course->id);
                $averageAttendance = collect($attendanceData)->avg('percentage');

                $data[] = [
                    'label' => $course->code,
                    'value' => round($averageAttendance, 2),
                    'student_count' => $students->count(),
                    'course_name' => $course->name,
                ];
            }
        }

        return [
            'type' => 'bar',
            'title' => 'Attendance by Course',
            'data' => $data,
        ];
    }

    /**
     * Get attendance data grouped by staff
     *
     * @param int $departmentId
     * @param int $days
     * @return array
     */
    private function getAttendanceByStaff(int $departmentId, int $days): array
    {
        $lecturers = Lecturer::where('department_id', $departmentId)
            ->where('is_active', true)
            ->with('user')
            ->get();

        $data = [];
        foreach ($lecturers as $lecturer) {
            $sessions = AttendanceSession::whereHas('classroom', function ($query) use ($lecturer) {
                $query->where('lecturer_id', $lecturer->id);
            })
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

            $totalAttendance = Attendance::whereHas('attendanceSession.classroom', function ($query) use ($lecturer) {
                $query->where('lecturer_id', $lecturer->id);
            })
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

            $data[] = [
                'label' => $lecturer->user->name ?? 'Unknown',
                'value' => $sessions,
                'total_attendance' => $totalAttendance,
                'staff_id' => $lecturer->staff_id,
            ];
        }

        return [
            'type' => 'bar',
            'title' => 'Sessions Conducted by Staff',
            'data' => $data,
        ];
    }

    /**
     * Get monthly attendance trend
     *
     * @param int $departmentId
     * @return array
     */
    private function getMonthlyAttendanceTrend(int $departmentId): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $totalSessions = AttendanceSession::whereHas('classroom.lecturer', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

            $totalAttendance = Attendance::whereHas('attendanceSession.classroom.lecturer', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

            $attendanceRate = $totalSessions > 0 ? ($totalAttendance / $totalSessions) * 100 : 0;

            $months[] = [
                'label' => $date->format('M Y'),
                'value' => round($attendanceRate, 2),
                'total_sessions' => $totalSessions,
                'total_attendance' => $totalAttendance,
            ];
        }

        return [
            'type' => 'line',
            'title' => 'Monthly Attendance Trend',
            'data' => $months,
        ];
    }

    /**
     * Clear dashboard cache for a department
     *
     * @param int $departmentId
     * @return void
     */
    public function clearCache(int $departmentId): void
    {
        $cacheKeys = [
            "hod_dashboard_overview_{$departmentId}",
            "hod_attendance_chart_{$departmentId}_level_30",
            "hod_attendance_chart_{$departmentId}_course_30",
            "hod_attendance_chart_{$departmentId}_staff_30",
            "hod_attendance_chart_{$departmentId}_monthly_30",
            "hod_threshold_compliance_{$departmentId}_75",
            "hod_performance_metrics_{$departmentId}_30",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}