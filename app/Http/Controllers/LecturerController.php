<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LecturerController extends Controller
{
    public function dashboard(Request $request)
    {
        // Use authenticated lecturer if available, else fallback to first lecturer
        $lecturer = auth()->user();
        if (!$lecturer || !$lecturer instanceof \App\Models\Lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return view('lecturer.dashboard')->withErrors(['No lecturer found.']);
        }

        $classes = $lecturer->classrooms()->with('students')->get();
        $totalStudents = $classes->sum(function($class) {
            return $class->students->count();
        });
        $recentAttendances = $lecturer->attendances()->with(['student', 'classroom'])->latest()->take(5)->get();

        // For demo, calculate today's attendance as a percentage (dummy if no data)
        $todayAttendance = 0;
        $today = now()->toDateString();
        $todayAttendances = $lecturer->attendances()->whereDate('attendances.created_at', $today)->count();
        $todayTotal = $classes->sum(function($class) {
            return $class->students->count();
        });
        if ($todayTotal > 0) {
            $todayAttendance = round(($todayAttendances / $todayTotal) * 100);
        }

        return view('lecturer.dashboard', [
            'lecturer' => $lecturer,
            'stats' => [
                'total_classes' => $classes->count(),
                'total_students' => $totalStudents,
                'today_attendance' => $todayAttendance,
                'recent_activity' => $recentAttendances->count(),
            ],
            'classes' => $classes,
            'recent_attendances' => $recentAttendances,
        ]);
    }

    public function manageAttendance(Request $request)
    {
        $lecturer = auth()->user();
        if (!$lecturer || !$lecturer instanceof \App\Models\Lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return view('lecturer.attendance')->withErrors(['No lecturer found.']);
        }

        // Get all classes for this lecturer, with students
        $classes = $lecturer->classrooms()->with(['students', 'attendanceSessions' => function($q) {
            $q->whereDate('start_time', now()->toDateString());
        }])->get();

        // For each class, determine if a session is active today
        $classData = $classes->map(function($class) {
            $sessionToday = $class->attendanceSessions->sortByDesc('start_time')->first();
            return [
                'id' => $class->id,
                'name' => $class->class_name,
                'code' => $class->course_code,
                'level' => $class->level,
                'student_count' => $class->students->count(),
                'session_today' => $sessionToday,
            ];
        });

        return view('lecturer.attendance', [
            'lecturer' => $lecturer,
            'classes' => $classData,
        ]);
    }

    public function startAttendance(Request $request, $classId)
    {
        $lecturer = auth()->user();
        if (!$lecturer || !$lecturer instanceof \App\Models\Lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No lecturer found.'], 404);
            }
            return redirect()->back()->withErrors(['No lecturer found.']);
        }
        $class = $lecturer->classrooms()->where('id', $classId)->first();
        if (!$class) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Class not found.'], 404);
            }
            return redirect()->back()->withErrors(['Class not found.']);
        }
        $existingSession = $class->attendanceSessions()->whereDate('start_time', now()->toDateString())->orderByDesc('start_time')->first();
        if ($existingSession) {
            if (!$existingSession->is_active) {
                // Reactivate the session (merge/continue)
                $existingSession->is_active = true;
                $existingSession->end_time = null;
                // Optionally: generate a new code on restart
                // $existingSession->code = strtoupper(\Illuminate\Support\Str::random(6));
                $existingSession->save();
            }
            $quickUrl = url('/student/attendance-capture?code=' . $existingSession->code);
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Session reactivated.', 'redirect' => route('lecturer.attendance.session', ['classId' => $classId]), 'quick_url' => $quickUrl]);
            }
            return redirect()->route('lecturer.attendance.session', ['classId' => $classId])->with('quick_url', $quickUrl);
        }
        // If no session exists for today, create a new one
        do {
            $newCode = strtoupper(\Illuminate\Support\Str::random(6));
        } while (\App\Models\AttendanceSession::where('code', $newCode)->exists());
        $session = new \App\Models\AttendanceSession();
        $session->classroom_id = $class->id;
        $session->lecturer_id = $lecturer->id;
        $session->start_time = now();
        $session->is_active = true;
        $session->code = $newCode;
        $session->save();
        $quickUrl = url('/student/attendance-capture?code=' . $session->code);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Attendance session started!', 'redirect' => route('lecturer.attendance.session', ['classId' => $classId]), 'quick_url' => $quickUrl]);
        }
        return redirect()->route('lecturer.attendance.session', ['classId' => $classId])->with('quick_url', $quickUrl);
    }

    public function attendanceSession(Request $request, $classId)
    {
        $lecturer = auth()->user();
        if (!$lecturer || !$lecturer instanceof \App\Models\Lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return redirect()->back()->withErrors(['No lecturer found.']);
        }
        $class = $lecturer->classrooms()->with(['students', 'attendanceSessions' => function($q) {
            $q->whereDate('start_time', now()->toDateString());
        }])->where('id', $classId)->first();
        if (!$class) {
            return redirect()->back()->withErrors(['Class not found.']);
        }
        $session = $class->attendanceSessions->sortByDesc('start_time')->first();
        if (!$session) {
            return redirect()->back()->withErrors(['No active session for today.']);
        }
        // Get only students of the class's level
        $students = $class->students
            ->where('level', $class->level)
            ->where('department', 'Computer Science');
        $attendances = $session->attendances()->with('student')->get();
        return view('lecturer.attendance_session', [
            'lecturer' => $lecturer,
            'class' => $class,
            'session' => $session,
            'attendances' => $attendances,
            'students' => $students,
        ]);
    }

    public function markAttendance(Request $request, $sessionId)
    {
        $session = \App\Models\AttendanceSession::findOrFail($sessionId);
        $class = $session->classroom;
        // Only allow for today and if session is active
        if ($session->start_time->toDateString() !== now()->toDateString() || !$session->is_active) {
            return redirect()->back()->withErrors(['Session is not active.']);
        }
        if ($request->has('mark_present')) {
            $studentId = $request->input('mark_present');
            \App\Models\Attendance::updateOrCreate([
                'attendance_session_id' => $session->id,
                'student_id' => $studentId,
            ], [
                'status' => 'present',
            ]);
        } elseif ($request->has('mark_absent')) {
            $studentId = $request->input('mark_absent');
            \App\Models\Attendance::updateOrCreate([
                'attendance_session_id' => $session->id,
                'student_id' => $studentId,
            ], [
                'status' => 'absent',
            ]);
        }
        return redirect()->back();
    }

    public function endAttendance(Request $request, $sessionId)
    {
        $session = \App\Models\AttendanceSession::findOrFail($sessionId);
        if ($session->start_time->toDateString() !== now()->toDateString() || !$session->is_active) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Session is not active.'], 400);
            }
            return redirect()->back()->withErrors(['Session is not active.']);
        }
        $session->is_active = false;
        $session->end_time = now();
        $session->save();
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Attendance session ended.']);
        }
        return redirect()->route('lecturer.attendance.manage')->with('success', 'Attendance session ended.');
    }

    public function studentsPage(Request $request)
    {
        $lecturer = auth()->user();
        if (!$lecturer || !$lecturer instanceof \App\Models\Lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return view('lecturer.students')->withErrors(['No lecturer found.']);
        }
        // Get all students assigned to any of the lecturer's classes
        $classIds = $lecturer->classrooms()->pluck('id');
        $students = \App\Models\Student::whereHas('classrooms', function($q) use ($classIds) {
            $q->whereIn('classroom_id', $classIds);
        })->get();
        return view('lecturer.students', [
            'lecturer' => $lecturer,
            'students' => $students,
        ]);
    }

    public function classesPage(Request $request)
    {
        $lecturer = auth()->user();
        if (!$lecturer || !$lecturer instanceof \App\Models\Lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return view('lecturer.classes')->withErrors(['No lecturer found.']);
        }
        $classes = $lecturer->classrooms()->with('students')->get();
        return view('lecturer.classes', [
            'lecturer' => $lecturer,
            'classes' => $classes,
        ]);
    }

    public function liveAttendanceApi($sessionId)
    {
        $session = \App\Models\AttendanceSession::findOrFail($sessionId);
        $class = $session->classroom;
        $students = $class->students
            ->where('level', $class->level)
            ->where('department', 'Computer Science');
        $attendances = $session->attendances()->get();
        $studentData = $students->map(function($student) use ($attendances) {
            $attendance = $attendances->where('student_id', $student->id)->first();
            return [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'matric_number' => $student->matric_number,
                'status' => $attendance && $attendance->status === 'present' ? 'present' : 'absent',
            ];
        })->values();
        $present = $studentData->where('status', 'present')->count();
        $total = $students->count();
        $absent = $total - $present;
        $percent = $total > 0 ? round(($present / $total) * 100) : 0;
        return response()->json([
            'students' => $studentData,
            'present' => $present,
            'absent' => $absent,
            'total' => $total,
            'percent' => $percent,
        ]);
    }

    public function updateGeoLocation(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:attendance_sessions,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $session = \App\Models\AttendanceSession::find($request->session_id);
        $session->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Recalibrate the geofence location for an attendance session
     */
    public function recalibrateLocation(Request $request, $sessionId)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|integer',
        ]);
        $session = \App\Models\AttendanceSession::findOrFail($sessionId);
        // Only allow the session's lecturer to recalibrate
        if ($session->lecturer_id !== auth()->id()) {
            abort(403);
        }
        $session->latitude = $request->latitude;
        $session->longitude = $request->longitude;
        if ($request->filled('radius')) {
            $session->radius = $request->radius;
        }
        $session->save();
        return response()->json(['success' => true, 'message' => 'Geofence recalibrated.']);
    }

    public function classDetail($classId)
    {
        $class = \App\Models\Classroom::with(['students', 'lecturer'])->find($classId);
        if (!$class) {
            return view('lecturer.class_detail')->withErrors(['Class not found.']);
        }
        $attendances = \App\Models\Attendance::with('student')
            ->where('classroom_id', $class->id)
            ->orderByDesc('captured_at')
            ->get();
        return view('lecturer.class_detail', [
            'class' => $class,
            'attendances' => $attendances,
        ]);
    }
}
