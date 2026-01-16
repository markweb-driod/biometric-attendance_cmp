<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamEligibility;
use App\Models\Student;
use App\Models\Course;
use App\Models\Attendance;
use App\Services\ExamEligibilityService;
use App\Services\AttendanceCalculationService;

class HodExamEligibilityController extends Controller
{
    protected $examEligibilityService;

    public function __construct(AttendanceCalculationService $attendanceService)
    {
        $this->middleware(['auth:hod', 'hod.role']);
        $this->examEligibilityService = new ExamEligibilityService($attendanceService);
    }

    /**
     * Display exam eligibility dashboard
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $hod = Auth::guard('hod')->user();
        
        // Get exam eligibility data
        $eligibilityDataResult = $this->examEligibilityService->getEligibilityData($hod->department_id, $filters);
        $eligibilityStats = $this->examEligibilityService->getEligibilityStats($hod->department_id, $filters);
        $atRiskStudents = $this->examEligibilityService->getAtRiskStudents($hod->department_id, $filters);
        $recentOverrides = $this->examEligibilityService->getRecentOverrides($hod->department_id);
        
        // Extract data from service result
        $eligibilityData = $eligibilityDataResult['data'] ?? collect();
        
        // Get filter options
        $filterOptions = $this->getFilterOptions($hod->department_id);
        
        return view('hod.exam.eligibility', compact(
            'eligibilityData',
            'eligibilityStats',
            'atRiskStudents',
            'recentOverrides',
            'filterOptions',
            'filters'
        ));
    }

    /**
     * Get exam eligibility data via API
     */
    public function getEligibilityData(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $hod = Auth::guard('hod')->user();
        
        $data = $this->examEligibilityService->getEligibilityData($hod->department_id, $filters);
        
        return response()->json($data);
    }

    /**
     * Get eligibility statistics
     */
    public function getEligibilityStats(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $hod = Auth::guard('hod')->user();
        
        $stats = $this->examEligibilityService->getEligibilityStats($hod->department_id, $filters);
        
        return response()->json($stats);
    }

    /**
     * Get at-risk students
     */
    public function getAtRiskStudents(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $hod = Auth::guard('hod')->user();
        
        $students = $this->examEligibilityService->getAtRiskStudents($hod->department_id, $filters);
        
        return response()->json($students);
    }

    /**
     * Prepare waiver (store data and redirect to 2FA)
     */
    public function prepareWaiver(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'reason' => 'required|string|min:50|max:1000',
            'document_ref' => 'nullable|string|max:255'
        ]);

        // Store waiver data in session
        session([
            'waiver_data' => [
                'student_id' => $request->student_id,
                'course_id' => $request->course_id,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'reason' => $request->reason,
                'document_ref' => $request->document_ref,
                'hod_id' => Auth::guard('hod')->id(),
                'timestamp' => now()
            ]
        ]);

        // Set intended URL after 2FA
        session(['hod_2fa_intended_url' => route('hod.exam.api.eligibility.execute-waiver')]);

        return response()->json([
            'success' => true,
            'redirect_url' => route('hod.two-factor.show')
        ]);
    }

    /**
     * Execute waiver after 2FA verification
     */
    public function executeWaiver(Request $request)
    {
        // Check 2FA verification
        if (!session('hod_2fa_verified')) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication required',
                'requires_2fa' => true,
                'redirect' => route('hod.two-factor.show')
            ], 403);
        }

        // Get waiver data from session
        $waiverData = session('waiver_data');

        if (!$waiverData) {
            return response()->json([
                'success' => false,
                'message' => 'Waiver data not found. Please start the waiver process again.'
            ], 400);
        }

        $hod = Auth::guard('hod')->user();

        // Execute the waiver
        $result = $this->examEligibilityService->waiveEligibility(
            $waiverData['student_id'],
            $waiverData['course_id'],
            $waiverData['semester'],
            $waiverData['academic_year'],
            $waiverData['reason'],
            $waiverData['document_ref'],
            $hod->id
        );

        // Clear waiver data from session
        session()->forget('waiver_data');
        session()->forget('hod_2fa_verified');
        session()->forget('hod_2fa_verified_at');

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Eligibility requirement waived successfully',
                'data' => $result['data']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /**
     * Override exam eligibility
     */
    public function overrideEligibility(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'override_reason' => 'required|string|max:500',
            'status' => 'required|in:eligible,ineligible'
        ]);

        $hod = Auth::guard('hod')->user();
        
        $result = $this->examEligibilityService->overrideEligibility(
            $request->student_id,
            $request->course_id,
            $request->semester,
            $request->academic_year,
            $request->override_reason,
            $request->status,
            $hod->id
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Exam eligibility overridden successfully',
                'data' => $result['data']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /**
     * Bulk override exam eligibility
     */
    public function bulkOverrideEligibility(Request $request)
    {
        $request->validate([
            'overrides' => 'required|array|min:1',
            'overrides.*.student_id' => 'required|exists:students,id',
            'overrides.*.course_id' => 'required|exists:courses,id',
            'overrides.*.semester' => 'required|string',
            'overrides.*.academic_year' => 'required|string',
            'overrides.*.override_reason' => 'required|string|max:500',
            'overrides.*.status' => 'required|in:eligible,ineligible'
        ]);

        $hod = Auth::guard('hod')->user();
        
        $results = $this->examEligibilityService->bulkOverrideEligibility(
            $request->overrides,
            $hod->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Bulk override completed',
            'results' => $results
        ]);
    }

    /**
     * Calculate eligibility for all students
     */
    public function calculateEligibility(Request $request)
    {
        $request->validate([
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'course_id' => 'nullable|exists:courses,id'
        ]);

        $hod = Auth::guard('hod')->user();
        
        $result = $this->examEligibilityService->calculateEligibilityForDepartment(
            $hod->department_id,
            $request->semester,
            $request->academic_year,
            $request->course_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Eligibility calculation completed',
            'data' => $result
        ]);
    }

    /**
     * Export eligibility data
     */
    public function exportData(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $hod = Auth::guard('hod')->user();
        
        $exportData = $this->examEligibilityService->exportEligibilityData($hod->department_id, $filters);
        
        return response()->json($exportData);
    }

    /**
     * Save eligibility configuration
     */
    public function saveConfiguration(Request $request)
    {
        $request->validate([
            'threshold' => 'required|numeric|min:0|max:100',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
        ]);

        // Store configuration in session or database
        // For now, just store in session
        session([
            'eligibility_threshold' => $request->threshold,
            'eligibility_semester' => $request->semester,
            'eligibility_academic_year' => $request->academic_year,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Configuration saved successfully',
            'config' => $request->only(['threshold', 'semester', 'academic_year']),
        ]);
    }

    /**
     * Get filters from request
     */
    private function getFiltersFromRequest(Request $request)
    {
        return [
            'semester' => $request->get('semester'),
            'academic_year' => $request->get('academic_year'),
            'course_id' => $request->get('course_id'),
            'level_id' => $request->get('level_id'),
            'status' => $request->get('status'),
            'attendance_threshold' => $request->get('attendance_threshold', 75),
            'search' => $request->get('search'),
            'sort_by' => $request->get('sort_by', 'student_name'),
            'sort_order' => $request->get('sort_order', 'asc'),
            'per_page' => $request->get('per_page', 25)
        ];
    }

    /**
     * Get filter options
     */
    private function getFilterOptions($departmentId)
    {
        return [
            'semesters' => ['First Semester', 'Second Semester', 'Summer Semester'],
            'academic_years' => ['2023/2024', '2024/2025', '2025/2026'],
            'courses' => Course::whereHas('classrooms', function($query) use ($departmentId) {
                $query->whereHas('lecturer', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            })->get(['id', 'code', 'name']),
            'levels' => \App\Models\AcademicLevel::all(['id', 'name']),
            'statuses' => ['eligible', 'ineligible', 'overridden']
        ];
    }
}











