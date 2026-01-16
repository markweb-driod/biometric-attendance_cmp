<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class OptimizedLandingController extends Controller
{
    /**
     * Optimized student landing page
     */
    public function studentLanding()
    {
        // Return view without stats to avoid database queries on homepage
        // "/" is just a login/landing page, stats are not needed
        return view('student.attendance_capture');
    }

    /**
     * Optimized lecturer landing page
     */
    public function lecturerLanding()
    {
        $stats = Cache::remember('lecturer_landing_stats', 300, function () {
            return [
                'total_lecturers' => \App\Models\Lecturer::where('is_active', true)->count(),
                'active_classes' => \App\Models\Classroom::where('is_active', true)->count(),
                'today_sessions' => \App\Models\AttendanceSession::whereDate('created_at', today())->count(),
            ];
        });

        return view('lecturer_landing', compact('stats'));
    }

    /**
     * Optimized superadmin landing page
     */
    public function superadminLanding()
    {
        $stats = Cache::remember('superadmin_landing_stats', 300, function () {
            return [
                'total_users' => \App\Models\User::where('is_active', true)->count(),
                'total_students' => \App\Models\Student::where('is_active', true)->count(),
                'total_lecturers' => \App\Models\Lecturer::where('is_active', true)->count(),
                'total_departments' => \App\Models\Department::where('is_active', true)->count(),
                'total_courses' => \App\Models\Course::where('is_active', true)->count(),
                'total_classrooms' => \App\Models\Classroom::where('is_active', true)->count(),
                'today_attendance' => \App\Models\Attendance::whereDate('captured_at', today())->count(),
                'active_sessions' => \App\Models\AttendanceSession::where('is_active', true)->count(),
            ];
        });

        return view('superadmin_landing', compact('stats'));
    }

    /**
     * Get system health status
     */
    public function getSystemHealth()
    {
        return Cache::remember('system_health', 60, function () {
            try {
                // Quick database check
                $dbStatus = \DB::select('SELECT 1')[0] ? 'healthy' : 'error';
                
                // Check storage space
                $storagePath = storage_path();
                $totalSpace = disk_total_space($storagePath);
                $freeSpace = disk_free_space($storagePath);
                $usagePercentage = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2);
                
                $storageStatus = $usagePercentage > 90 ? 'critical' : 
                               ($usagePercentage > 80 ? 'warning' : 'healthy');

                return [
                    'status' => 'operational',
                    'database' => $dbStatus,
                    'storage' => $storageStatus,
                    'storage_usage' => $usagePercentage,
                    'timestamp' => now()->toISOString()
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'timestamp' => now()->toISOString()
                ];
            }
        });
    }

    /**
     * Get quick statistics for dashboard
     */
    public function getQuickStats()
    {
        return Cache::remember('quick_stats', 300, function () {
            return [
                'students' => \App\Models\Student::where('is_active', true)->count(),
                'lecturers' => \App\Models\Lecturer::where('is_active', true)->count(),
                'departments' => \App\Models\Department::where('is_active', true)->count(),
                'courses' => \App\Models\Course::where('is_active', true)->count(),
                'classrooms' => \App\Models\Classroom::where('is_active', true)->count(),
                'today_attendance' => \App\Models\Attendance::whereDate('captured_at', today())->count(),
                'active_sessions' => \App\Models\AttendanceSession::where('is_active', true)->count(),
            ];
        });
    }
}
