<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Department;
use App\Models\AcademicLevel;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuperadminReportsController extends Controller
{
    /**
     * Display reports page
     */
    public function index()
    {
        // Get initial data for dropdowns
        $departments = Department::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $academicLevels = AcademicLevel::where('is_active', true)->orderBy('level_number')->get(['id', 'name', 'level_number']);
        $classrooms = Classroom::where('is_active', true)
            ->with(['course:id,course_name,course_code', 'lecturer.user:id,full_name'])
            ->orderBy('class_name')
            ->get(['id', 'class_name', 'course_id', 'lecturer_id']);
        
        return view('superadmin.reports', compact('departments', 'academicLevels', 'classrooms'));
    }

    /**
     * Get reports data (KPIs and table data)
     */
    public function getReportsData(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'classroom_id' => $request->input('classroom_id'),
            'department_id' => $request->input('department_id'),
            'academic_level_id' => $request->input('academic_level_id'),
            'status' => $request->input('status'),
            'search' => $request->input('search'),
            'page' => $request->input('page', 1),
        ];

        // Build query
        $query = Attendance::with([
            'student.user:id,full_name',
            'student:id,user_id,matric_number,department_id,academic_level_id',
            'student.department:id,name',
            'student.academicLevel:id,name,level_number',
            'classroom:id,class_name,course_id,lecturer_id',
            'classroom.course:id,course_name,course_code',
        ]);

        // Apply filters
        if ($filters['date_from']) {
            $query->whereDate('captured_at', '>=', $filters['date_from']);
        }
        if ($filters['date_to']) {
            $query->whereDate('captured_at', '<=', $filters['date_to']);
        }
        if ($filters['classroom_id']) {
            $query->where('classroom_id', $filters['classroom_id']);
        }
        if ($filters['department_id']) {
            $query->whereHas('student', function($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }
        if ($filters['academic_level_id']) {
            $query->whereHas('student', function($q) use ($filters) {
                $q->where('academic_level_id', $filters['academic_level_id']);
            });
        }
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        if ($filters['search']) {
            $search = $filters['search'];
            $query->whereHas('student.user', function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('matric_number', 'like', "%$search%");
            });
        }

        // Get KPIs
        $kpis = $this->calculateKPIs($filters);

        // Get paginated table data
        $perPage = 50;
        $attendances = $query->orderBy('captured_at', 'desc')->paginate($perPage);

        // Format table data
        $tableData = $attendances->map(function($attendance) {
            return [
                'id' => $attendance->id,
                'student_name' => $attendance->student->user->full_name ?? 'N/A',
                'matric_number' => $attendance->student->matric_number ?? 'N/A',
                'class_name' => $attendance->classroom->class_name ?? 'N/A',
                'course_name' => $attendance->classroom->course->course_name ?? 'N/A',
                'course_code' => $attendance->classroom->course->course_code ?? 'N/A',
                'academic_level' => $attendance->student->academicLevel->name ?? 'N/A',
                'department' => $attendance->student->department->name ?? 'N/A',
                'captured_at' => $attendance->captured_at ? $attendance->captured_at->format('Y-m-d H:i:s') : 'N/A',
                'status' => $attendance->status ?? 'present',
                'method' => $attendance->image_path ? 'Biometric' : 'Manual',
            ];
        });

        return response()->json([
            'success' => true,
            'kpis' => $kpis,
            'table_data' => $tableData,
            'pagination' => [
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'per_page' => $attendances->perPage(),
                'total' => $attendances->total(),
            ],
        ]);
    }

    /**
     * Calculate KPIs based on filters
     */
    private function calculateKPIs($filters)
    {
        // Build base query for KPIs
        $kpiQuery = Attendance::query();

        // Apply same filters as main query
        if ($filters['date_from']) {
            $kpiQuery->whereDate('captured_at', '>=', $filters['date_from']);
        }
        if ($filters['date_to']) {
            $kpiQuery->whereDate('captured_at', '<=', $filters['date_to']);
        }
        if ($filters['classroom_id']) {
            $kpiQuery->where('classroom_id', $filters['classroom_id']);
        }
        if ($filters['department_id']) {
            $kpiQuery->whereHas('student', function($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }
        if ($filters['academic_level_id']) {
            $kpiQuery->whereHas('student', function($q) use ($filters) {
                $q->where('academic_level_id', $filters['academic_level_id']);
            });
        }

        // Total sessions (unique classroom sessions per day)
        $sessions = $kpiQuery->clone()
            ->select(DB::raw('DATE(captured_at) as date'), 'classroom_id')
            ->groupBy(DB::raw('DATE(captured_at)'), 'classroom_id')
            ->get();
        $totalSessions = $sessions->count();

        // Total present count
        $presentCount = $kpiQuery->clone()->where('status', 'present')->count();

        // Total absentees (denied or missing)
        $absentCount = $kpiQuery->clone()->where('status', '!=', 'present')->count();

        // Calculate attendance rate
        $totalRecords = $presentCount + $absentCount;
        $attendanceRate = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : 0;

        // Top performing class
        $topClass = $kpiQuery->clone()
            ->select('classrooms.class_name', DB::raw('COUNT(*) as attendance_count'))
            ->join('classrooms', 'attendances.classroom_id', '=', 'classrooms.id')
            ->where('attendances.status', 'present')
            ->groupBy('classrooms.id', 'classrooms.class_name')
            ->orderBy('attendance_count', 'desc')
            ->first();

        return [
            'attendance_rate' => $attendanceRate,
            'total_sessions' => $totalSessions,
            'absentees' => $absentCount,
            'top_class' => $topClass ? $topClass->class_name : 'N/A',
        ];
    }

    /**
     * Export reports to CSV
     */
    public function exportCsv(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'classroom_id' => $request->input('classroom_id'),
            'department_id' => $request->input('department_id'),
            'academic_level_id' => $request->input('academic_level_id'),
            'status' => $request->input('status'),
            'search' => $request->input('search'),
        ];

        // Build query (same as getReportsData)
        $query = Attendance::with([
            'student.user:id,full_name',
            'student:id,user_id,matric_number,department_id,academic_level_id',
            'student.department:id,name',
            'student.academicLevel:id,name',
            'classroom:id,class_name,course_id',
            'classroom.course:id,course_name,course_code',
        ]);

        // Apply filters
        if ($filters['date_from']) {
            $query->whereDate('captured_at', '>=', $filters['date_from']);
        }
        if ($filters['date_to']) {
            $query->whereDate('captured_at', '<=', $filters['date_to']);
        }
        if ($filters['classroom_id']) {
            $query->where('classroom_id', $filters['classroom_id']);
        }
        if ($filters['department_id']) {
            $query->whereHas('student', function($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }
        if ($filters['academic_level_id']) {
            $query->whereHas('student', function($q) use ($filters) {
                $q->where('academic_level_id', $filters['academic_level_id']);
            });
        }
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        if ($filters['search']) {
            $search = $filters['search'];
            $query->whereHas('student.user', function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('matric_number', 'like', "%$search%");
            });
        }

        $attendances = $query->orderBy('captured_at', 'desc')->get();

        $filename = 'attendance_reports_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendances) {
            $handle = fopen('php://output', 'w');
            
            // Write CSV headers
            fputcsv($handle, [
                'Student Name',
                'Matric Number',
                'Department',
                'Academic Level',
                'Class Name',
                'Course Code',
                'Course Name',
                'Date & Time',
                'Status',
                'Method'
            ]);

            // Write data rows
            foreach ($attendances as $attendance) {
                fputcsv($handle, [
                    $attendance->student->user->full_name ?? 'N/A',
                    $attendance->student->matric_number ?? 'N/A',
                    $attendance->student->department->name ?? 'N/A',
                    $attendance->student->academicLevel->name ?? 'N/A',
                    $attendance->classroom->class_name ?? 'N/A',
                    $attendance->classroom->course->course_code ?? 'N/A',
                    $attendance->classroom->course->course_name ?? 'N/A',
                    $attendance->captured_at ? $attendance->captured_at->format('Y-m-d H:i:s') : 'N/A',
                    ucfirst($attendance->status ?? 'present'),
                    $attendance->image_path ? 'Biometric' : 'Manual',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get attendance trends for charts
     */
    public function getTrends(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from') ?: now()->subDays(30)->format('Y-m-d'),
            'date_to' => $request->input('date_to') ?: now()->format('Y-m-d'),
            'department_id' => $request->input('department_id'),
            'academic_level_id' => $request->input('academic_level_id'),
        ];

        $query = Attendance::query();

        if ($filters['date_from']) {
            $query->whereDate('captured_at', '>=', $filters['date_from']);
        }
        if ($filters['date_to']) {
            $query->whereDate('captured_at', '<=', $filters['date_to']);
        }
        if ($filters['department_id']) {
            $query->whereHas('student', function($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }
        if ($filters['academic_level_id']) {
            $query->whereHas('student', function($q) use ($filters) {
                $q->where('academic_level_id', $filters['academic_level_id']);
            });
        }

        $trends = $query->select(
            DB::raw('DATE(captured_at) as date'),
            DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count'),
            DB::raw('SUM(CASE WHEN status != "present" THEN 1 ELSE 0 END) as absent_count'),
            DB::raw('COUNT(*) as total_count')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json([
            'success' => true,
            'trends' => $trends,
        ]);
    }
}
