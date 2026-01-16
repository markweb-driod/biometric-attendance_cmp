<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\StudentAttendanceService;
use App\Services\ChartDataService;

class HodStudentMonitoringController extends Controller
{
    protected $studentAttendanceService;
    protected $chartDataService;

    public function __construct()
    {
        $this->middleware(['auth:hod', 'hod.role']);
    }

    /**
     * Display student monitoring dashboard
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        
        // Initialize services
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        $chartDataService = new ChartDataService();
        
        // Get data for the dashboard
        $studentAttendance = $studentAttendanceService->getStudentAttendanceData($filters);
        $weeklyTrends = $studentAttendanceService->getWeeklyStudentTrends($filters);
        $studentMetrics = $studentAttendanceService->getStudentPerformanceMetrics($filters);
        $attendanceAnalysis = $studentAttendanceService->getAttendanceAnalysis($filters);
        $courseSummary = $studentAttendanceService->getCourseAttendanceSummary($filters);
        
        // Get course attendance by time period for the main bar chart
        $courseAttendanceData = $studentAttendanceService->getCourseAttendanceByTimePeriod($filters);
        
        // Handle pagination for student metrics
        $pagination = null;
        if ($studentMetrics instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $pagination = $studentMetrics;
            $studentMetrics = $studentMetrics->items();
        }
        
        // Format data for charts
        // Main chart is now a bar chart showing courses on X-axis
        $studentChartData = $chartDataService->formatCourseAttendanceBarChart($courseAttendanceData, $filters);
        $studentPerformanceChartData = $chartDataService->formatStudentPerformanceChart($studentMetrics, $filters);
        $distributionChartData = $chartDataService->formatPerformanceDistributionChart($attendanceAnalysis);
        $riskChartData = $chartDataService->formatRiskAnalysisChart($attendanceAnalysis);
        
        // Get filter options
        $filterOptions = $this->getFilterOptions();
        
        return view('hod.monitoring.students', compact(
            'studentAttendance',
            'weeklyTrends',
            'studentMetrics',
            'attendanceAnalysis',
            'courseSummary',
            'studentChartData',
            'studentPerformanceChartData',
            'distributionChartData',
            'riskChartData',
            'filterOptions',
            'filters',
            'pagination'
        ));
    }

    /**
     * Get student attendance data via API
     */
    public function getStudentAttendanceData(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        $data = $studentAttendanceService->getStudentAttendanceData($filters);
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get weekly student trends via API
     */
    public function getWeeklyTrends(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        $chartDataService = new ChartDataService();
        $trends = $studentAttendanceService->getWeeklyStudentTrends($filters);
        $chartData = $chartDataService->formatStudentAttendanceChart($trends, $filters);
        
        return response()->json([
            'success' => true,
            'data' => $trends,
            'chartData' => $chartData
        ]);
    }

    /**
     * Get student performance metrics via API
     */
    public function getStudentMetrics(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        $chartDataService = new ChartDataService();
        
        // Handle pagination
        $metrics = $studentAttendanceService->getStudentPerformanceMetrics($filters);
        $metricsArray = $metrics instanceof \Illuminate\Pagination\LengthAwarePaginator ? $metrics->items() : $metrics;
        
        $chartData = $chartDataService->formatStudentPerformanceChart($metricsArray, $filters);
        
        return response()->json([
            'success' => true,
            'data' => $metrics,
            'chartData' => $chartData
        ]);
    }

    /**
     * Get attendance analysis via API
     */
    public function getAttendanceAnalysis(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        $chartDataService = new ChartDataService();
        $analysis = $studentAttendanceService->getAttendanceAnalysis($filters);
        $distributionChartData = $chartDataService->formatPerformanceDistributionChart($analysis);
        $riskChartData = $chartDataService->formatRiskAnalysisChart($analysis);
        
        return response()->json([
            'success' => true,
            'data' => $analysis,
            'distributionChartData' => $distributionChartData,
            'riskChartData' => $riskChartData
        ]);
    }

    /**
     * Get course attendance summary via API
     */
    public function getCourseSummary(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        $summary = $studentAttendanceService->getCourseAttendanceSummary($filters);
        
        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Get course attendance chart data via API (for time period filtering)
     */
    public function getCourseAttendanceChart(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        $chartDataService = new ChartDataService();
        
        $courseAttendanceData = $studentAttendanceService->getCourseAttendanceByTimePeriod($filters);
        $chartData = $chartDataService->formatCourseAttendanceBarChart($courseAttendanceData, $filters);
        
        return response()->json([
            'success' => true,
            'data' => $courseAttendanceData,
            'chartData' => $chartData
        ]);
    }

    /**
     * Get at-risk students
     */
    public function getAtRiskStudents(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        $analysis = $studentAttendanceService->getAttendanceAnalysis($filters);
        
        return response()->json([
            'success' => true,
            'data' => $analysis['at_risk_students'] ?? []
        ]);
    }

    /**
     * Get top performing students
     */
    public function getTopPerformers(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        $analysis = $studentAttendanceService->getAttendanceAnalysis($filters);
        
        return response()->json([
            'success' => true,
            'data' => $analysis['top_performers'] ?? []
        ]);
    }

    /**
     * Show fullscreen chart view
     */
    public function showChart(Request $request, $chartType)
    {
        $filters = $this->getFiltersFromRequest($request);
        
        // Initialize services
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        $chartDataService = new ChartDataService();
        
        // Get appropriate chart data based on chart type
        $chartData = null;
        $chartTitle = '';
        
        switch ($chartType) {
            case 'attendance':
                $courseAttendanceData = $studentAttendanceService->getCourseAttendanceByTimePeriod($filters);
                $chartData = $chartDataService->formatCourseAttendanceBarChart($courseAttendanceData, $filters);
                $chartTitle = 'Course Attendance';
                break;
            case 'performance':
                $studentMetrics = $studentAttendanceService->getStudentPerformanceMetrics($filters);
                $metricsArray = $studentMetrics instanceof \Illuminate\Pagination\LengthAwarePaginator ? $studentMetrics->items() : $studentMetrics;
                $chartData = $chartDataService->formatStudentPerformanceChart($metricsArray, $filters);
                $chartTitle = 'Student Performance';
                break;
            case 'risk':
                $attendanceAnalysis = $studentAttendanceService->getAttendanceAnalysis($filters);
                $chartData = $chartDataService->formatRiskAnalysisChart($attendanceAnalysis);
                $chartTitle = 'Risk Analysis';
                break;
            case 'distribution':
                $attendanceAnalysis = $studentAttendanceService->getAttendanceAnalysis($filters);
                $chartData = $chartDataService->formatPerformanceDistributionChart($attendanceAnalysis);
                $chartTitle = 'Attendance Distribution';
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
     * Export student data
     */
    public function exportData(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $format = $request->get('format', 'excel');
        $type = $request->get('type', 'attendance'); // attendance, performance, analysis
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        
        switch ($type) {
            case 'attendance':
                $data = $studentAttendanceService->getStudentAttendanceData($filters);
                break;
            case 'performance':
                $data = $studentAttendanceService->getStudentPerformanceMetrics($filters);
                break;
            case 'analysis':
                $data = $studentAttendanceService->getAttendanceAnalysis($filters);
                break;
            default:
                $data = $studentAttendanceService->getStudentAttendanceData($filters);
        }
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'format' => $format,
            'type' => $type
        ]);
    }

    /**
     * Clear cache for student monitoring
     */
    public function clearCache(Request $request)
    {
        $studentAttendanceService = new StudentAttendanceService(Auth::guard('hod')->user());
        $studentAttendanceService->clearCache();
        
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
            'attendance_threshold' => $request->get('attendance_threshold'),
            'performance_filter' => $request->get('performance_filter'),
            'risk_level' => $request->get('risk_level'),
            'time_period' => $request->get('time_period', 'all'), // all, semester, live
            'week' => $request->get('week'), // Specific week number (1-14)
            'performance_view_mode' => $request->get('performance_view_mode', 'aggregated'), // aggregated, individual
            'sort_by' => $request->get('sort_by', 'average_attendance_rate'),
            'sort_order' => $request->get('sort_order', 'desc'),
            'semester_start' => $request->get('semester_start'),
            'search' => $request->get('search'),
            'per_page' => $request->get('per_page', 15),
            'page' => $request->get('page', 1)
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
            'performance_filters' => [
                'all' => 'All Students',
                'top' => 'Top Performers (>90%)',
                'good' => 'Good Performers (80-90%)',
                'average' => 'Average Performers (70-80%)',
                'poor' => 'Poor Performers (60-70%)',
                'critical' => 'Critical Performers (<60%)'
            ],
            'attendance_thresholds' => [
                '90' => '90% and above',
                '80' => '80% and above',
                '70' => '70% and above',
                '60' => '60% and above',
                '50' => '50% and above'
            ],
            'risk_levels' => [
                'all' => 'All Risk Levels',
                'high' => 'High Risk',
                'medium' => 'Medium Risk',
                'low' => 'Low Risk'
            ]
        ];
    }
}
