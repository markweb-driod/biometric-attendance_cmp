<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\Classroom;

class SuperadminAttendanceController extends Controller
{
    // Show all attendance attempts (present and denied) with location
    public function index(Request $request)
    {
        $attendances = Attendance::with(['student', 'attendanceSession', 'classroom'])
            ->orderByDesc('captured_at')
            ->paginate(50);
        return view('superadmin.attendance_audit', compact('attendances'));
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
        $total = \App\Models\Attendance::count();
        $present = \App\Models\Attendance::where('status', 'present')->count();
        $denied = \App\Models\Attendance::where('status', 'denied')->count();
        $uniqueStudents = \App\Models\Attendance::distinct('student_id')->count('student_id');
        $spoofers = \App\Models\Attendance::select('student_id')
            ->where('status', 'denied')
            ->groupBy('student_id')
            ->havingRaw('COUNT(*) >= 3')
            ->get()->count();
        return response()->json([
            'total' => $total,
            'present' => $present,
            'denied' => $denied,
            'unique_students' => $uniqueStudents,
            'suspected_spoofers' => $spoofers,
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
        $total = \App\Models\Attendance::count();
        $present = \App\Models\Attendance::where('status', 'present')->count();
        $biometric = \App\Models\Attendance::whereNotNull('image_path')->count();
        $lastAttendance = \App\Models\Attendance::orderByDesc('captured_at')->value('captured_at');
        return response()->json([
            'total' => $total,
            'present' => $present,
            'biometric' => $biometric,
            'last_attendance' => $lastAttendance,
        ]);
    }
} 