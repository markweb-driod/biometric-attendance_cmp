<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class AttendanceMonitoringController extends Controller
{
    /**
     * Display the attendance monitoring dashboard
     */
    public function index(Request $request): View
    {
        return view('attendance-monitoring.dashboard');
    }

    /**
     * Get chart data for the attendance monitoring dashboard
     */
    public function getChartData(Request $request): JsonResponse
    {
        $type = $request->get('type', 'daily');
        $chart = $request->get('chart', 'bar');

        try {
            $data = $this->generateSampleChartData($type, $chart);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load chart data',
                'message' => config('app.debug') ? $e->getMessage() : 'Please try again later',
                'fallback_data' => $this->getFallbackChartData($type)
            ], 500);
        }
    }

    /**
     * Generate sample chart data for demonstration
     */
    private function generateSampleChartData(string $type, string $chart): array
    {
        if ($type === 'daily') {
            return $this->getDailyChartData();
        } else {
            return $this->getWeeklyChartData();
        }
    }

    /**
     * Get daily attendance chart data
     */
    private function getDailyChartData(): array
    {
        $labels = [];
        $attendanceData = [];
        $additionalInfo = [];
        $drillDownData = [];

        // Generate data for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M j');
            
            // Generate random attendance data
            $totalStudents = rand(80, 120);
            $present = rand(60, $totalStudents);
            $absent = $totalStudents - $present;
            $attendanceRate = ($present / $totalStudents) * 100;
            
            $attendanceData[] = round($attendanceRate, 1);
            
            $additionalInfo[] = [
                'totalStudents' => $totalStudents,
                'present' => $present,
                'absent' => $absent,
                'late' => rand(0, 5)
            ];

            // Generate drill-down data
            $drillDownData[] = [
                'title' => $date->format('F j, Y'),
                'totalStudents' => $totalStudents,
                'present' => $present,
                'absent' => $absent,
                'late' => rand(0, 5),
                'attendanceRate' => $attendanceRate,
                'subjects' => [
                    ['name' => 'Mathematics', 'attendanceRate' => rand(70, 95)],
                    ['name' => 'Physics', 'attendanceRate' => rand(65, 90)],
                    ['name' => 'Chemistry', 'attendanceRate' => rand(75, 92)],
                ],
                'studentList' => $this->generateSampleStudentList($present, $absent)
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Attendance Rate',
                    'data' => $attendanceData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                ]
            ],
            'additionalInfo' => $additionalInfo,
            'drillDownData' => $drillDownData,
            'summary' => [
                'totalSessions' => 7,
                'averageAttendance' => round(array_sum($attendanceData) / count($attendanceData), 1),
                'trend' => rand(-5, 8) // Random trend for demo
            ]
        ];
    }

    /**
     * Get weekly attendance chart data
     */
    private function getWeeklyChartData(): array
    {
        $labels = [];
        $attendanceData = [];
        $additionalInfo = [];
        $drillDownData = [];

        // Generate data for the last 4 weeks
        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            $labels[] = $startOfWeek->format('M j') . ' - ' . $endOfWeek->format('M j');
            
            // Generate random weekly attendance data
            $totalStudents = rand(400, 600); // Weekly total
            $present = rand(300, $totalStudents);
            $absent = $totalStudents - $present;
            $attendanceRate = ($present / $totalStudents) * 100;
            
            $attendanceData[] = round($attendanceRate, 1);
            
            $additionalInfo[] = [
                'totalStudents' => $totalStudents,
                'present' => $present,
                'absent' => $absent,
                'late' => rand(5, 25)
            ];

            // Generate drill-down data
            $drillDownData[] = [
                'title' => 'Week of ' . $startOfWeek->format('F j, Y'),
                'totalStudents' => $totalStudents,
                'present' => $present,
                'absent' => $absent,
                'late' => rand(5, 25),
                'attendanceRate' => $attendanceRate,
                'subjects' => [
                    ['name' => 'Mathematics', 'attendanceRate' => rand(70, 95)],
                    ['name' => 'Physics', 'attendanceRate' => rand(65, 90)],
                    ['name' => 'Chemistry', 'attendanceRate' => rand(75, 92)],
                    ['name' => 'Biology', 'attendanceRate' => rand(68, 88)],
                    ['name' => 'English', 'attendanceRate' => rand(72, 94)],
                ],
                'studentList' => null // Don't show individual students for weekly view
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Weekly Attendance Rate',
                    'data' => $attendanceData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                ]
            ],
            'additionalInfo' => $additionalInfo,
            'drillDownData' => $drillDownData,
            'summary' => [
                'totalSessions' => 4,
                'averageAttendance' => round(array_sum($attendanceData) / count($attendanceData), 1),
                'trend' => rand(-3, 6) // Random trend for demo
            ]
        ];
    }

    /**
     * Generate sample student list for drill-down
     */
    private function generateSampleStudentList(int $present, int $absent): array
    {
        $students = [];
        $sampleNames = [
            'John Smith', 'Jane Doe', 'Michael Johnson', 'Sarah Wilson', 'David Brown',
            'Emily Davis', 'James Miller', 'Jessica Garcia', 'Robert Martinez', 'Ashley Anderson',
            'Christopher Taylor', 'Amanda Thomas', 'Matthew Jackson', 'Jennifer White', 'Daniel Harris'
        ];

        // Add present students
        for ($i = 0; $i < min($present, 10); $i++) {
            $students[] = [
                'name' => $sampleNames[$i % count($sampleNames)],
                'status' => rand(0, 10) > 8 ? 'late' : 'present',
                'time' => rand(0, 10) > 8 ? '08:' . rand(10, 30) : '08:00'
            ];
        }

        // Add some absent students
        for ($i = 0; $i < min($absent, 5); $i++) {
            $students[] = [
                'name' => $sampleNames[($present + $i) % count($sampleNames)],
                'status' => 'absent',
                'time' => null
            ];
        }

        return $students;
    }

    /**
     * Get fallback chart data when main data fails to load
     */
    private function getFallbackChartData(string $type): array
    {
        return [
            'labels' => $type === 'daily' ? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'] : ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            'datasets' => [
                [
                    'label' => 'Attendance Rate',
                    'data' => [75, 80, 78, 82, 85],
                    'backgroundColor' => 'rgba(156, 163, 175, 0.8)',
                    'borderColor' => 'rgba(156, 163, 175, 1)',
                    'borderWidth' => 2,
                ]
            ],
            'additionalInfo' => [],
            'drillDownData' => [],
            'summary' => [
                'totalSessions' => 5,
                'averageAttendance' => 80,
                'trend' => 0
            ]
        ];
    }

    /**
     * Filter attendance records for the monitoring table
     */
    public function filter(Request $request): JsonResponse
    {
        $query = \App\Models\Attendance::with(['student.user', 'classroom.course', 'session']);

        // Search by Student Name or Matric
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('matric_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by Date (Optional)
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('captured_at', $request->date);
        }

        $attendances = $query->orderBy('captured_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json($attendances);
    }
}