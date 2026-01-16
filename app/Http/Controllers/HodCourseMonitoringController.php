<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CourseMonitoringService;
use App\Services\ChartDataService;

class HodCourseMonitoringController extends Controller
{
    protected $courseMonitoringService;
    protected $chartDataService;

    public function __construct()
    {
        $this->middleware(['auth:hod', 'hod.role']);
    }

    /**
     * Display course monitoring dashboard
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        
        // Initialize services
        $courseMonitoringService = new CourseMonitoringService(Auth::guard('hod')->user());
        $chartDataService = new ChartDataService();
        
        // Get data for the dashboard
        $coursePerformance = $courseMonitoringService->getCoursePerformanceData($filters);
        $weeklyTrends = $courseMonitoringService->getWeeklyAttendanceTrends($filters);
        $lecturerMetrics = $courseMonitoringService->getLecturerPerformanceMetrics($filters);
        $performanceAnalysis = $courseMonitoringService->getPerformanceAnalysis($filters);
        
        // Format data for charts
        $courseChartData = $chartDataService->formatCoursePerformanceChart($weeklyTrends, $filters);
        $lecturerChartData = $chartDataService->formatLecturerPerformanceChart($lecturerMetrics);
        $distributionChartData = $chartDataService->formatPerformanceDistributionChart($performanceAnalysis);
        
        // Get filter options
        $filterOptions = $this->getFilterOptions();
        
        return view('hod.monitoring.courses', compact(
            'coursePerformance',
            'weeklyTrends',
            'lecturerMetrics',
            'performanceAnalysis',
            'courseChartData',
            'lecturerChartData',
            'distributionChartData',
            'filterOptions',
            'filters'
        ));
    }

    /**
     * Get course performance data via API
     */
    public function getCoursePerformanceData(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $courseMonitoringService = new CourseMonitoringService(Auth::guard('hod')->user());
        $data = $courseMonitoringService->getCoursePerformanceData($filters);
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get weekly attendance trends via API
     */
    public function getWeeklyTrends(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $courseMonitoringService = new CourseMonitoringService(Auth::guard('hod')->user());
        $chartDataService = new ChartDataService();
        $trends = $courseMonitoringService->getWeeklyAttendanceTrends($filters);
        $chartData = $chartDataService->formatCoursePerformanceChart($trends, $filters);
        
        return response()->json([
            'success' => true,
            'data' => $trends,
            'chartData' => $chartData
        ]);
    }

    /**
     * Get lecturer performance metrics via API
     */
    public function getLecturerMetrics(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $courseMonitoringService = new CourseMonitoringService(Auth::guard('hod')->user());
        $chartDataService = new ChartDataService();
        $metrics = $courseMonitoringService->getLecturerPerformanceMetrics($filters);
        $chartData = $chartDataService->formatLecturerPerformanceChart($metrics);
        
        return response()->json([
            'success' => true,
            'data' => $metrics,
            'chartData' => $chartData
        ]);
    }

    /**
     * Get performance analysis via API
     */
    public function getPerformanceAnalysis(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $courseMonitoringService = new CourseMonitoringService(Auth::guard('hod')->user());
        $chartDataService = new ChartDataService();
        $analysis = $courseMonitoringService->getPerformanceAnalysis($filters);
        $chartData = $chartDataService->formatPerformanceDistributionChart($analysis);
        
        return response()->json([
            'success' => true,
            'data' => $analysis,
            'chartData' => $chartData
        ]);
    }

    /**
     * Show fullscreen chart view
     */
    public function showChart(Request $request, $chartType)
    {
        $filters = $this->getFiltersFromRequest($request);
        
        // Initialize services
        $courseMonitoringService = new CourseMonitoringService(Auth::guard('hod')->user());
        $chartDataService = new ChartDataService();
        
        // Get appropriate chart data based on chart type
        $chartData = null;
        $chartTitle = '';
        
        switch ($chartType) {
            case 'performance':
                $weeklyTrends = $courseMonitoringService->getWeeklyAttendanceTrends($filters);
                $chartData = $chartDataService->formatCoursePerformanceChart($weeklyTrends, $filters);
                $chartTitle = 'Course Performance Trends';
                break;
            case 'lecturer':
                $lecturerMetrics = $courseMonitoringService->getLecturerPerformanceMetrics($filters);
                $chartData = $chartDataService->formatLecturerPerformanceChart($lecturerMetrics);
                $chartTitle = 'Lecturer Performance';
                break;
            case 'distribution':
                $performanceAnalysis = $courseMonitoringService->getPerformanceAnalysis($filters);
                $chartData = $chartDataService->formatPerformanceDistributionChart($performanceAnalysis);
                $chartTitle = 'Performance Distribution';
                break;
            default:
                abort(404, 'Chart type not found');
        }
        
        $filterOptions = $this->getFilterOptions();
        
        return view('hod.monitoring.chart-fullscreen', compact(
            'chartData',
            'chartTitle',
            'filterOptions',
            'filters',
            'chartType'
        ));
    }

    /**
     * Export course performance data
     */
    public function exportData(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $format = $request->get('format', 'excel');
        $courseMonitoringService = new CourseMonitoringService(Auth::guard('hod')->user());
        $data = $courseMonitoringService->getCoursePerformanceData($filters);
        
        // This would integrate with Laravel Excel
        // For now, return JSON
        return response()->json([
            'success' => true,
            'data' => $data,
            'format' => $format
        ]);
    }

    /**
     * Clear cache for course monitoring
     */
    public function clearCache(Request $request)
    {
        $courseMonitoringService = new CourseMonitoringService(Auth::guard('hod')->user());
        $courseMonitoringService->clearCache();
        
        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully'
        ]);
    }

    /**
     * Get filters from request
     */
    private function getFiltersFromRequest(Request $request)
    {
        return [
            'academic_level' => $request->get('academic_level'),
            'semester' => $request->get('semester'),
            'academic_year' => $request->get('academic_year'),
            'course_type' => $request->get('course_type'),
            'lecturer_status' => $request->get('lecturer_status'),
            'attendance_threshold' => $request->get('attendance_threshold'),
            'performance_filter' => $request->get('performance_filter'),
            'sort_by' => $request->get('sort_by', 'average_attendance_rate'),
            'sort_order' => $request->get('sort_order', 'desc'),
            'semester_start' => $request->get('semester_start')
        ];
    }

    /**
     * Get filter options
     */
    private function getFilterOptions()
    {
        return [
            'academic_levels' => [
                '100' => '100 Level',
                '200' => '200 Level',
                '300' => '300 Level',
                '400' => '400 Level',
                '500' => '500 Level'
            ],
            'semesters' => [
                '1' => 'First Semester',
                '2' => 'Second Semester',
                '3' => 'Summer Semester'
            ],
            'academic_years' => [
                '2023/2024' => '2023/2024',
                '2024/2025' => '2024/2025',
                '2025/2026' => '2025/2026'
            ],
            'course_types' => [
                'core' => 'Core Courses',
                'elective' => 'Elective Courses',
                'general' => 'General Studies'
            ],
            'lecturer_statuses' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
                'on_leave' => 'On Leave'
            ],
            'performance_filters' => [
                'all' => 'All Performance',
                'top' => 'Top Performers (>85%)',
                'average' => 'Average Performers (70-85%)',
                'low' => 'Low Performers (<70%)'
            ],
            'attendance_thresholds' => [
                '90' => '90% and above',
                '80' => '80% and above',
                '70' => '70% and above',
                '60' => '60% and above',
                '50' => '50% and above'
            ]
        ];
    }
}
