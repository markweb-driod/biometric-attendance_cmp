<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\AttendanceSession;

class ReportController extends Controller
{
    // API: Return filtered analytics for reports page
    public function reportData(Request $request)
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
        // KPIs
        $totalSessions = AttendanceSession::count();
        $totalAttendances = Attendance::count();
        $attendanceRate = $totalSessions > 0 ? round(($totalAttendances / $totalSessions) * 100, 1) : 0;
        $absentees = Attendance::where('status', '!=', 'present')->count();
        $topClass = Classroom::withCount('attendances')->orderByDesc('attendances_count')->first();
        return response()->json([
            'success' => true,
            'data' => $records,
            'kpis' => [
                'attendance_rate' => $attendanceRate,
                'total_sessions' => $totalSessions,
                'absentees' => $absentees,
                'top_class' => $topClass ? ($topClass->course_code ?? $topClass->class_name) : '-',
            ]
        ]);
    }

    // API: Export filtered analytics as CSV
    public function exportCsv(Request $request)
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
        $records = $query->latest('captured_at')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reports_export.csv"',
        ];
        $callback = function() use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Student', 'Matric Number', 'Class', 'Level', 'Date & Time', 'Status', 'Method'
            ]);
            foreach ($records as $a) {
                fputcsv($handle, [
                    $a->student ? $a->student->full_name : '',
                    $a->student ? $a->student->matric_number : '',
                    $a->classroom ? $a->classroom->class_name : '',
                    $a->student ? $a->student->academic_level : '',
                    $a->captured_at ? $a->captured_at->format('Y-m-d H:i:s') : '',
                    ucfirst($a->status ?? 'Present'),
                    $a->image_path ? 'Biometric' : 'Manual',
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }
}
