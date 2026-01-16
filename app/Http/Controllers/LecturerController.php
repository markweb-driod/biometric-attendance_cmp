<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\SessionMonitoringService;

class LecturerController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $lecturer = \App\Models\Lecturer::with(['user', 'department'])->where('staff_id', $request->staff_id)
            ->where('is_active', true)
            ->first();

        if (!$lecturer || !$lecturer->user) {
            return redirect()->back()->withErrors(['Invalid staff ID or lecturer not found.']);
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $lecturer->user->password)) {
            return redirect()->back()->withErrors(['Invalid password.']);
        }

        // Login the lecturer
        auth('lecturer')->login($lecturer);

        return redirect()->route('lecturer.dashboard');
    }

    public function logout(Request $request)
    {
        // Mark session as ended
        $sessionMonitoringService = new SessionMonitoringService();
        $sessionMonitoringService->endSession(session()->getId());
        
        Auth::guard('lecturer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function dashboard(Request $request)
    {
        // Use authenticated lecturer if available, else fallback to first lecturer
        $lecturer = auth('lecturer')->user();
        if (!$lecturer) {
            $lecturer = \App\Models\Lecturer::with('user:id,full_name')->first();
        }
        if (!$lecturer) {
            return view('lecturer.dashboard')->withErrors(['No lecturer found.']);
        }

        // Cache dashboard data for 5 minutes
        $cacheKey = 'lecturer_dashboard_' . $lecturer->id;
        $dashboardData = \Cache::remember($cacheKey, 300, function () use ($lecturer) {
            // Only get classrooms for assigned courses
            $assignedCourseIds = $lecturer->courses()->pluck('courses.id');
            $classes = $lecturer->classrooms()
                ->whereIn('course_id', $assignedCourseIds)
                ->with(['students', 'course:id,course_name'])
                ->select(['id', 'class_name', 'course_id', 'lecturer_id', 'is_active'])
                ->where('is_active', true)
                ->get();
                
            $totalStudents = $classes->sum(function($class) {
                return $class->students->count();
            });
            
            $recentAttendances = $lecturer->attendances()
                ->with(['student', 'classroom:id,class_name'])
                ->select(['attendances.id', 'student_id', 'classroom_id', 'captured_at', 'status'])
                ->latest('captured_at')
                ->take(5)
                ->get();

            // Calculate today's attendance
            $todayAttendance = 0;
            $today = now()->toDateString();
            $todayAttendances = $lecturer->attendances()
                ->whereDate('captured_at', $today)
                ->count();
            $todayTotal = $totalStudents;
            if ($todayTotal > 0) {
                $todayAttendance = round(($todayAttendances / $todayTotal) * 100);
            }

            return [
                'classes' => $classes,
                'recent_attendances' => $recentAttendances,
                'stats' => [
                    'total_classes' => $classes->count(),
                    'total_students' => $totalStudents,
                    'today_attendance' => $todayAttendance,
                    'recent_activity' => $recentAttendances->count(),
                ]
            ];
        });

        return view('lecturer.dashboard', [
            'lecturer' => $lecturer,
            'stats' => $dashboardData['stats'],
            'classes' => $dashboardData['classes'],
            'recent_attendances' => $dashboardData['recent_attendances'],
        ]);
    }

    public function manageAttendance(Request $request)
    {
        $lecturer = auth('lecturer')->user();
        if (!$lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return view('lecturer.attendance')->withErrors(['No lecturer found.']);
        }

        // Get all classes for this lecturer (only for assigned courses), with students and course data
        $assignedCourseIds = $lecturer->courses()->pluck('courses.id');
        $classes = $lecturer->classrooms()
            ->whereIn('course_id', $assignedCourseIds)
            ->with(['students', 'course.academicLevel', 'attendanceSessions' => function($q) {
                $q->whereDate('start_time', now()->toDateString());
            }])
            ->get();

        // For each class, determine if a session is active today
        $classData = $classes->map(function($class) {
            $sessionToday = $class->attendanceSessions->sortByDesc('start_time')->first();
            return [
                'id' => $class->id,
                'name' => $class->class_name,
                'code' => $class->course ? $class->course->course_code : 'N/A',
                'level' => $class->course && $class->course->academicLevel ? $class->course->academicLevel->name : 'N/A',
                'student_count' => $class->students->count(),
                'session_today' => $sessionToday,
            ];
        });

        $venues = \App\Models\Venue::where('department_id', $lecturer->department_id)
            ->orWhereNull('department_id')
            ->where('is_active', true)
            ->get();

        return view('lecturer.attendance', [
            'lecturer' => $lecturer,
            'classes' => $classData,
            'venues' => $venues,
        ]);
    }

    public function startAttendance(Request $request, $classId)
    {
        $lecturer = auth('lecturer')->user();
        if (!$lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No lecturer found.'], 404);
            }
            return redirect()->back()->withErrors(['No lecturer found.']);
        }

        // Validate venue and duration
        $request->validate([
            'venue_id' => 'nullable|exists:venues,id',
            'duration' => 'nullable|integer|min:1',
        ]);

        // Verify the class belongs to an assigned course
        $assignedCourseIds = $lecturer->courses()->pluck('courses.id');
        $class = $lecturer->classrooms()
            ->where('id', $classId)
            ->whereIn('course_id', $assignedCourseIds)
            ->first();
        if (!$class) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Class not found or you are not assigned to this course.'], 404);
            }
            return redirect()->back()->withErrors(['Class not found or you are not assigned to this course.']);
        }
        $existingSession = $class->attendanceSessions()->whereDate('start_time', now()->toDateString())->orderByDesc('start_time')->first();
        if ($existingSession) {
            if (!$existingSession->is_active) {
                // Reactivate the session (merge/continue)
                $existingSession->is_active = true;
                $existingSession->end_time = null;
                // Update venue and duration if provided
                if ($request->filled('venue_id')) {
                    $existingSession->venue_id = $request->venue_id;
                }
                if ($request->filled('duration')) {
                    $existingSession->duration = $request->duration;
                }
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
        $session->session_name = $class->class_name . ' - ' . now()->format('M d, Y');
        $session->start_time = now();
        $session->is_active = true;
        $session->code = $newCode;
        $session->venue_id = $request->venue_id;
        $session->duration = $request->duration;
        $session->save();
        $quickUrl = url('/student/attendance-capture?code=' . $session->code);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Attendance session started!', 'redirect' => route('lecturer.attendance.session', ['classId' => $classId]), 'quick_url' => $quickUrl]);
        }
        return redirect()->route('lecturer.attendance.session', ['classId' => $classId])->with('quick_url', $quickUrl);
    }

    public function attendanceSession(Request $request, $classId)
    {
        $lecturer = auth('lecturer')->user();
        if (!$lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return redirect()->back()->withErrors(['No lecturer found.']);
        }
        // Verify the class belongs to an assigned course
        $assignedCourseIds = $lecturer->courses()->pluck('courses.id');
        $class = $lecturer->classrooms()
            ->whereIn('course_id', $assignedCourseIds)
            ->with(['students', 'attendanceSessions' => function($q) {
                $q->whereDate('start_time', now()->toDateString())->where('is_active', true);
            }])
            ->where('id', $classId)
            ->first();
        if (!$class) {
            return redirect()->back()->withErrors(['Class not found or you are not assigned to this course.']);
        }
        $session = $class->attendanceSessions->sortByDesc('start_time')->first();
        if (!$session) {
            return redirect()->back()->withErrors(['No active session for today.']);
        }
        
        // Generate quick URL for this session
        $quickUrl = url('/student/attendance-capture?code=' . $session->code);
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
        ])->with('quick_url', $quickUrl);
    }

    public function markAttendance(Request $request, $sessionId)
    {
        try {
            $session = \App\Models\AttendanceSession::findOrFail($sessionId);
            $class = $session->classroom;
            
            // Only allow for today and if session is active
            if ($session->start_time->toDateString() !== now()->toDateString() || !$session->is_active) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Session is not active.'
                    ], 403);
                }
                return redirect()->back()->withErrors(['Session is not active.']);
            }
            
            $studentId = $request->input('student_id');
            $status = $request->input('status'); // 'present' or 'absent'
            
            if (!$studentId || !in_array($status, ['present', 'absent'])) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid parameters.'
                    ], 422);
                }
                return redirect()->back()->withErrors(['Invalid parameters.']);
            }
            
            \App\Models\Attendance::updateOrCreate([
                'attendance_session_id' => $session->id,
                'student_id' => $studentId,
            ], [
                'status' => $status,
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance marked as ' . $status . '.'
                ]);
            }
            
            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error marking attendance: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->withErrors(['Error marking attendance: ' . $e->getMessage()]);
        }
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


    public function classesPage(Request $request)
    {
        $lecturer = auth('lecturer')->user();
        if (!$lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return view('lecturer.classes')->withErrors(['No lecturer found.']);
        }
        // Only show classes for assigned courses
        $assignedCourseIds = $lecturer->courses()->pluck('courses.id');
        $classes = $lecturer->classrooms()
            ->whereIn('course_id', $assignedCourseIds)
            ->with(['students', 'course.academicLevel'])
            ->get();
        return view('lecturer.classes', [
            'lecturer' => $lecturer,
            'classes' => $classes,
        ]);
    }

    public function liveAttendanceApi($sessionId, Request $request)
    {
        $session = \App\Models\AttendanceSession::with(['classroom.students.user', 'attendances'])->findOrFail($sessionId);
        $class = $session->classroom;
        $students = $class->students;
        $attendances = $session->attendances;
        
        $studentData = $students->map(function($student) use ($attendances) {
            $attendance = $attendances->where('student_id', $student->id)->first();
            return [
                'id' => $student->id,
                'full_name' => $student->user ? $student->user->full_name : 'Unknown',
                'matric_number' => $student->matric_number,
                'status' => $attendance && $attendance->status === 'present' ? 'present' : 'absent',
            ];
        })->values();

        // Filtering
        $search = $request->query('search');
        $status = $request->query('status');
        if ($search) {
            $studentData = $studentData->filter(function($s) use ($search) {
                return stripos($s['full_name'], $search) !== false || stripos($s['matric_number'], $search) !== false;
            })->values();
        }
        if ($status && in_array($status, ['present', 'absent'])) {
            $studentData = $studentData->where('status', $status)->values();
        }

        // Pagination
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);
        $total = $studentData->count();
        $lastPage = (int) ceil($total / $perPage);
        $page = max(1, min($page, $lastPage > 0 ? $lastPage : 1));
        $paginated = $studentData->slice(($page - 1) * $perPage, $perPage)->values();

        $present = $studentData->where('status', 'present')->count();
        $absent = $studentData->where('status', 'absent')->count();
        $percent = $total > 0 ? round(($present / $total) * 100) : 0;
        return response()->json([
            'students' => $paginated,
            'present' => $present,
            'absent' => $absent,
            'total' => $total,
            'percent' => $percent,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage,
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
        $lecturer = auth('lecturer')->user();
        if (!$lecturer) {
            return redirect()->route('login')->withErrors(['Please log in as a lecturer.']);
        }

        // Verify the class belongs to an assigned course
        $assignedCourseIds = $lecturer->courses()->pluck('courses.id');
        $class = \App\Models\Classroom::with(['students', 'lecturer'])
            ->where('id', $classId)
            ->whereIn('course_id', $assignedCourseIds)
            ->where('lecturer_id', $lecturer->id)
            ->first();
        if (!$class) {
            return view('lecturer.class_detail')->withErrors(['Class not found or you are not assigned to this course.']);
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

    public function studentsPage(Request $request)
    {
        $lecturer = auth('lecturer')->user();
        if (!$lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return redirect()->back()->withErrors(['No lecturer found.']);
        }

        // Get all students from classes managed by this lecturer
        $students = \App\Models\Student::whereHas('classrooms', function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })
        ->with(['user:id,full_name,email', 'department:id,name', 'academicLevel:id,name', 'classrooms' => function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id)->with('course:id,course_name,course_code');
        }])
        ->where('is_active', true)
        ->orderBy('matric_number')
        ->get();

        // Apply search filter if provided
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $students = $students->filter(function($student) use ($searchTerm) {
                return stripos($student->user->full_name, $searchTerm) !== false ||
                       stripos($student->matric_number, $searchTerm) !== false ||
                       stripos($student->user->email, $searchTerm) !== false;
            });
        }

        // Group students by course for better organization
        $studentsByCourse = $students->groupBy(function($student) {
            return $student->classrooms->first()->course->course_name ?? 'Unknown Course';
        });

        // Get course statistics
        $courseStats = [];
        foreach ($studentsByCourse as $courseName => $courseStudents) {
            $courseStats[$courseName] = [
                'total_students' => $courseStudents->count(),
                'course_code' => $courseStudents->first()->classrooms->first()->course->course_code ?? 'N/A',
                'class_name' => $courseStudents->first()->classrooms->first()->class_name ?? 'N/A',
            ];
        }

        return view('lecturer.students', [
            'lecturer' => $lecturer,
            'students' => $students,
            'studentsByCourse' => $studentsByCourse,
            'courseStats' => $courseStats,
        ]);
    }

    public function studentDetail(Request $request, $studentId)
    {
        $lecturer = auth('lecturer')->user();
        if (!$lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return redirect()->back()->withErrors(['No lecturer found.']);
        }

        // Get the student with all related data
        $student = \App\Models\Student::with([
            'user:id,full_name,email',
            'department:id,name',
            'academicLevel:id,name',
            'classrooms' => function($query) use ($lecturer) {
                $query->where('lecturer_id', $lecturer->id)
                      ->with(['course:id,course_name,course_code', 'lecturer.user:id,full_name']);
            }
        ])->findOrFail($studentId);

        // Check if the student is enrolled in any of the lecturer's classes
        if ($student->classrooms->isEmpty()) {
            return redirect()->route('lecturer.students')->withErrors(['Student is not enrolled in any of your classes.']);
        }

        return view('lecturer.student_detail', [
            'lecturer' => $lecturer,
            'student' => $student,
        ]);
    }

    public function studentAttendance(Request $request, $studentId)
    {
        $lecturer = auth('lecturer')->user();
        if (!$lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return redirect()->back()->withErrors(['No lecturer found.']);
        }

        // Get the student
        $student = \App\Models\Student::with(['user', 'department', 'academicLevel', 'classrooms' => function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id)->with('course');
        }])->findOrFail($studentId);

        // Check if the student is enrolled in any of the lecturer's classes
        if ($student->classrooms->isEmpty()) {
            return redirect()->back()->withErrors(['Student is not enrolled in any of your classes.']);
        }

        // Get attendance records for this student in the lecturer's classes
        $classIds = $lecturer->classrooms()->pluck('id');
        $attendances = \App\Models\Attendance::where('student_id', $studentId)
            ->whereIn('classroom_id', $classIds)
            ->with(['classroom.course', 'attendanceSession'])
            ->orderByDesc('captured_at')
            ->get();

        // Calculate attendance statistics
        $totalSessions = $attendances->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $attendancePercentage = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 2) : 0;

        return view('lecturer.student_attendance', [
            'lecturer' => $lecturer,
            'student' => $student,
            'attendances' => $attendances,
            'totalSessions' => $totalSessions,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'lateCount' => $lateCount,
            'attendancePercentage' => $attendancePercentage,
        ]);
    }

    public function reportsPage(Request $request)
    {
        $lecturer = auth('lecturer')->user();
        if (!$lecturer) {
            $lecturer = \App\Models\Lecturer::first();
        }
        if (!$lecturer) {
            return redirect()->back()->withErrors(['No lecturer found.']);
        }

        // Get all classes for this lecturer (only for assigned courses)
        $assignedCourseIds = $lecturer->courses()->pluck('courses.id');
        $classIds = $lecturer->classrooms()
            ->whereIn('course_id', $assignedCourseIds)
            ->pluck('id');
        
        // Get attendance statistics for charts
        $attendanceStats = \App\Models\Attendance::whereIn('classroom_id', $classIds)
            ->select(\DB::raw('DATE(captured_at) as date'), \DB::raw('COUNT(*) as count'), \DB::raw('status'))
            ->where('captured_at', '>=', now()->subDays(30))
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        // Get attendance by class for pie chart
        $classStats = \App\Models\Attendance::whereIn('classroom_id', $classIds)
            ->select('classroom_id', \DB::raw('COUNT(*) as count'))
            ->groupBy('classroom_id')
            ->get();

        // Only get classes for assigned courses
        $assignedCourseIds = $lecturer->courses()->pluck('courses.id');
        $classes = $lecturer->classrooms()
            ->whereIn('course_id', $assignedCourseIds)
            ->with('course:id,course_name,course_code')
            ->get();
        $classAttendanceData = $classes->map(function($class) use ($classStats) {
            $stat = $classStats->where('classroom_id', $class->id)->first();
            return [
                'name' => $class->course->course_name ?? $class->class_name,
                'count' => $stat->count ?? 0
            ];
        });

        // Weekly attendance trend
        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $count = \App\Models\Attendance::whereIn('classroom_id', $classIds)
                ->whereDate('captured_at', $dateStr)
                ->count();
            $weeklyTrend[$date->format('D')] = $count;
        }

        return view('lecturer.reports', [
            'lecturer' => $lecturer,
            'classes' => $classes,
            'weeklyTrend' => $weeklyTrend,
            'classAttendanceData' => $classAttendanceData,
        ]);
    }
}
