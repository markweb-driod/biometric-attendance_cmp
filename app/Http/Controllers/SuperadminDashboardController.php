<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Classroom;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\SystemSetting;

class SuperadminDashboardController extends Controller
{
    public function index()
    {
        // Get real statistics
        $stats = $this->getDashboardStats();
        
        return view('superadmin.dashboard', compact('stats'));
    }

    public function getStats()
    {
        $stats = $this->getDashboardStats();
        return response()->json($stats);
    }

    public function faceConfigForm()
    {
        $provider = SystemSetting::getValue('face_provider', config('face.provider'));
        $apiKey = SystemSetting::getValue('faceplusplus_api_key', config('face.faceplusplus_api_key'));
        $apiSecret = SystemSetting::getValue('faceplusplus_api_secret', config('face.faceplusplus_api_secret'));
        return view('superadmin.settings', compact('provider', 'apiKey', 'apiSecret'));
    }

    public function updateFaceConfig(Request $request)
    {
        $request->validate([
            'face_provider' => 'required|string',
            'faceplusplus_api_key' => 'nullable|string',
            'faceplusplus_api_secret' => 'nullable|string',
        ]);
        SystemSetting::setValue('face_provider', $request->face_provider);
        SystemSetting::setValue('faceplusplus_api_key', $request->faceplusplus_api_key);
        SystemSetting::setValue('faceplusplus_api_secret', $request->faceplusplus_api_secret);
        return redirect()->route('superadmin.face-config')->with('success', 'Face verification settings updated!');
    }

    public function testFacePP(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'api_secret' => 'required|string',
        ]);
        $apiKey = $request->input('api_key');
        $apiSecret = $request->input('api_secret');
        // Use a tiny sample image (1x1 transparent PNG)
        $sampleBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/w8AAn8B9p6Q2wAAAABJRU5ErkJggg==';
        $response = \Http::asForm()->post('https://api-us.faceplusplus.com/facepp/v3/detect', [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'image_base64' => $sampleBase64,
        ]);
        $result = $response->json();
        if ($response->ok() && isset($result['faces'])) {
            return response()->json(['success' => true, 'message' => 'Face++ credentials are valid and API is reachable.']);
        } else {
            $msg = $result['error_message'] ?? 'Unknown error.';
            return response()->json(['success' => false, 'message' => 'Face++ test failed: ' . $msg]);
        }
    }

    private function getDashboardStats()
    {
        // Get counts
        $totalStudents = Student::count();
        $totalLecturers = Lecturer::count();
        $totalClasses = Classroom::count();
        
        // Calculate attendance rate
        $totalSessions = AttendanceSession::count();
        $totalAttendances = Attendance::count();
        $attendanceRate = $totalSessions > 0 ? round(($totalAttendances / ($totalSessions * 50)) * 100, 1) : 0; // Assuming 50 students per session average
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities();
        
        // Get system health
        $systemHealth = $this->getSystemHealth();
        
        // Get top performing classes
        $topClasses = $this->getTopPerformingClasses();
        
        // Get attendance trends
        $attendanceTrends = $this->getAttendanceTrends();
        
        return [
            'kpis' => [
                'students' => $totalStudents,
                'lecturers' => $totalLecturers,
                'classes' => $totalClasses,
                'attendance_rate' => $attendanceRate
            ],
            'recent_activities' => $recentActivities,
            'system_health' => $systemHealth,
            'top_classes' => $topClasses,
            'attendance_trends' => $attendanceTrends
        ];
    }

    private function getRecentActivities()
    {
        $activities = [];
        
        // Get recent student uploads (based on created_at)
        $recentStudents = Student::orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($recentStudents as $student) {
            $activities[] = [
                'type' => 'student_upload',
                'message' => "Student {$student->first_name} {$student->last_name} added",
                'time' => $student->created_at->diffForHumans(),
                'icon' => 'user-plus'
            ];
        }
        
        // Get recent lecturers
        $recentLecturers = Lecturer::orderBy('created_at', 'desc')
            ->limit(2)
            ->get();
            
        foreach ($recentLecturers as $lecturer) {
            $activities[] = [
                'type' => 'lecturer_added',
                'message' => "Lecturer {$lecturer->first_name} {$lecturer->last_name} added",
                'time' => $lecturer->created_at->diffForHumans(),
                'icon' => 'user-tie'
            ];
        }
        
        // Get recent classes
        $recentClasses = Classroom::orderBy('created_at', 'desc')
            ->limit(2)
            ->get();
            
        foreach ($recentClasses as $class) {
            $activities[] = [
                'type' => 'class_created',
                'message' => "Class {$class->class_name} created",
                'time' => $class->created_at->diffForHumans(),
                'icon' => 'book'
            ];
        }
        
        // Sort by time and limit to 8 most recent
        usort($activities, function($a, $b) {
            return strtotime($a['time']) - strtotime($b['time']);
        });
        
        return array_slice($activities, 0, 8);
    }

    private function getSystemHealth()
    {
        $health = [];
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $health['database'] = 'healthy';
        } catch (\Exception $e) {
            $health['database'] = 'error';
        }
        
        // Check recent activity
        $lastActivity = Student::orderBy('created_at', 'desc')->first();
        if ($lastActivity && $lastActivity->created_at->diffInHours(now()) < 24) {
            $health['activity'] = 'active';
        } else {
            $health['activity'] = 'inactive';
        }
        
        // Check attendance sessions
        $todaySessions = AttendanceSession::whereDate('created_at', today())->count();
        $health['sessions_today'] = $todaySessions;
        
        return $health;
    }

    private function getTopPerformingClasses()
    {
        return Classroom::withCount('students')
            ->orderBy('students_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($class) {
                return [
                    'name' => $class->class_name,
                    'student_count' => $class->students_count,
                    'lecturer' => $class->lecturer ? $class->lecturer->first_name . ' ' . $class->lecturer->last_name : 'No Lecturer'
                ];
            });
    }

    private function getAttendanceTrends()
    {
        $trends = [];
        
        // Get last 7 days attendance data
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $sessions = AttendanceSession::whereDate('created_at', $date)->count();
            $attendances = Attendance::whereDate('created_at', $date)->count();
            
            $trends[] = [
                'date' => $date->format('M d'),
                'sessions' => $sessions,
                'attendances' => $attendances
            ];
        }
        
        return $trends;
    }
} 