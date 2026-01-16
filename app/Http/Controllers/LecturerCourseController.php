<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LecturerCourseController extends Controller
{
    /**
     * Constructor - ensure lecturer is authenticated
     */
    public function __construct()
    {
        $this->middleware('auth:lecturer');
    }

    /**
     * Display lecturer's assigned courses page
     */
    public function index(Request $request)
    {
        $lecturer = Auth::guard('lecturer')->user();

        if (!$lecturer) {
            return redirect()->route('login')->withErrors(['Please log in as a lecturer.']);
        }

        // Get assigned courses
        $courses = $lecturer->courses()
            ->with(['departments:id,name', 'academicLevel:id,name', 'semester:id,name'])
            ->orderBy('course_code')
            ->get();

        // Get statistics
        $stats = [
            'total_courses' => $courses->count(),
            'total_classrooms' => $lecturer->assignedClassrooms()->count(),
            'active_classrooms' => $lecturer->assignedClassrooms()->where('is_active', true)->count(),
        ];

        return view('lecturer.courses.index', compact('lecturer', 'courses', 'stats'));
    }

    /**
     * Get assigned courses (API)
     */
    public function getAssignedCourses(Request $request)
    {
        $lecturer = Auth::guard('lecturer')->user();

        if (!$lecturer) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $courses = $lecturer->courses()
            ->with(['departments:id,name', 'academicLevel:id,name', 'semester:id,name'])
            ->orderBy('course_code')
            ->get()
            ->map(function($course) use ($lecturer) {
                // Get classrooms for this course managed by this lecturer
                $classrooms = Classroom::where('course_id', $course->id)
                    ->where('lecturer_id', $lecturer->id)
                    ->withCount('students')
                    ->get();

                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'course_name' => $course->course_name,
                    'description' => $course->description,
                    'credit_units' => $course->credit_units,
                    'departments' => $course->departments->pluck('name')->all(),
                    'academic_level' => $course->academicLevel->name ?? 'N/A',
                    'semester' => $course->semester->name ?? 'N/A',
                    'classrooms_count' => $classrooms->count(),
                    'active_classrooms_count' => $classrooms->where('is_active', true)->count(),
                    'total_students' => $classrooms->sum('students_count'),
                    'assigned_at' => $course->pivot->assigned_at,
                ];
            });

        return response()->json(['success' => true, 'courses' => $courses]);
    }

    /**
     * Get details for a specific assigned course
     */
    public function show(Request $request, $courseId)
    {
        $lecturer = Auth::guard('lecturer')->user();

        if (!$lecturer) {
            return redirect()->route('login')->withErrors(['Please log in as a lecturer.']);
        }

        // Verify lecturer is assigned to this course
        if (!$lecturer->isAssignedToCourse($courseId)) {
            return redirect()->route('lecturer.courses.index')
                ->withErrors(['You are not assigned to this course.']);
        }

        $course = Course::with(['departments:id,name', 'academicLevel:id,name', 'semester:id,name'])
            ->findOrFail($courseId);

        // Get classrooms for this course managed by this lecturer
        $classrooms = Classroom::where('course_id', $courseId)
            ->where('lecturer_id', $lecturer->id)
            ->with(['students:id,matric_number', 'attendanceSessions' => function($query) {
                $query->orderBy('start_time', 'desc')->limit(5);
            }])
            ->withCount(['students', 'attendances'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_classrooms' => $classrooms->count(),
            'active_classrooms' => $classrooms->where('is_active', true)->count(),
            'total_students' => $classrooms->sum('students_count'),
            'total_attendances' => $classrooms->sum('attendances_count'),
        ];

        return view('lecturer.courses.show', compact('lecturer', 'course', 'classrooms', 'stats'));
    }

    /**
     * Get course details (API)
     */
    public function getCourseDetails(Request $request, $courseId)
    {
        $lecturer = Auth::guard('lecturer')->user();

        if (!$lecturer) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify lecturer is assigned to this course
        if (!$lecturer->isAssignedToCourse($courseId)) {
            return response()->json(['error' => 'You are not assigned to this course.'], 403);
        }

        $course = Course::with(['departments:id,name', 'academicLevel:id,name', 'semester:id,name'])
            ->findOrFail($courseId);

        // Get classrooms for this course managed by this lecturer
        $classrooms = Classroom::where('course_id', $courseId)
            ->where('lecturer_id', $lecturer->id)
            ->withCount(['students', 'attendances'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($classroom) {
                return [
                    'id' => $classroom->id,
                    'class_name' => $classroom->class_name,
                    'pin' => $classroom->pin,
                    'schedule' => $classroom->schedule,
                    'academic_year' => $classroom->academic_year,
                    'is_active' => $classroom->is_active,
                    'students_count' => $classroom->students_count,
                    'attendances_count' => $classroom->attendances_count,
                ];
            });

        return response()->json([
            'success' => true,
            'course' => [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'description' => $course->description,
                'credit_units' => $course->credit_units,
                'departments' => $course->departments->pluck('name')->all(),
                'academic_level' => $course->academicLevel->name ?? 'N/A',
                'semester' => $course->semester->name ?? 'N/A',
                'assigned_at' => $lecturer->allCourses()->where('courses.id', $courseId)->first()->pivot->assigned_at,
            ],
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * Get classrooms for an assigned course
     */
    public function getCourseClassrooms(Request $request, $courseId)
    {
        $lecturer = Auth::guard('lecturer')->user();

        if (!$lecturer) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify lecturer is assigned to this course
        if (!$lecturer->isAssignedToCourse($courseId)) {
            return response()->json(['error' => 'You are not assigned to this course.'], 403);
        }

        $classrooms = Classroom::where('course_id', $courseId)
            ->where('lecturer_id', $lecturer->id)
            ->with(['students:id,matric_number', 'attendanceSessions' => function($query) {
                $query->where('is_active', true)->orderBy('start_time', 'desc');
            }])
            ->withCount(['students', 'attendances'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($classroom) {
                $activeSession = $classroom->attendanceSessions->first();
                
                return [
                    'id' => $classroom->id,
                    'class_name' => $classroom->class_name,
                    'pin' => $classroom->pin,
                    'schedule' => $classroom->schedule,
                    'academic_year' => $classroom->academic_year,
                    'is_active' => $classroom->is_active,
                    'students_count' => $classroom->students_count,
                    'attendances_count' => $classroom->attendances_count,
                    'has_active_session' => $activeSession ? true : false,
                    'active_session_code' => $activeSession ? $activeSession->code : null,
                ];
            });

        return response()->json(['success' => true, 'classrooms' => $classrooms]);
    }

    /**
     * Get course statistics
     */
    public function getCourseStatistics(Request $request, $courseId)
    {
        $lecturer = Auth::guard('lecturer')->user();

        if (!$lecturer) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify lecturer is assigned to this course
        if (!$lecturer->isAssignedToCourse($courseId)) {
            return response()->json(['error' => 'You are not assigned to this course.'], 403);
        }

        // Get classrooms for this course
        $classroomIds = Classroom::where('course_id', $courseId)
            ->where('lecturer_id', $lecturer->id)
            ->pluck('id');

        // Calculate statistics
        $stats = [
            'total_classrooms' => $classroomIds->count(),
            'active_classrooms' => Classroom::whereIn('id', $classroomIds)
                ->where('is_active', true)
                ->count(),
            'total_students' => DB::table('class_student')
                ->whereIn('classroom_id', $classroomIds)
                ->where('enrollment_status', 'enrolled')
                ->distinct('student_id')
                ->count('student_id'),
            'total_attendances' => DB::table('attendances')
                ->whereIn('classroom_id', $classroomIds)
                ->count(),
            'recent_attendances' => DB::table('attendances')
                ->whereIn('classroom_id', $classroomIds)
                ->whereDate('captured_at', '>=', now()->subDays(7))
                ->count(),
            'today_attendances' => DB::table('attendances')
                ->whereIn('classroom_id', $classroomIds)
                ->whereDate('captured_at', today())
                ->count(),
        ];

        return response()->json(['success' => true, 'stats' => $stats]);
    }

    /**
     * Get available courses for classroom creation (only assigned courses)
     */
    public function getAvailableCourses(Request $request)
    {
        $lecturer = Auth::guard('lecturer')->user();

        if (!$lecturer) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $courses = $lecturer->courses()
            ->where('is_active', true)
            ->with(['departments:id,name', 'academicLevel:id,name'])
            ->select(['id', 'course_code', 'course_name', 'academic_level_id', 'credit_units'])
            ->orderBy('course_code')
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'course_name' => $course->course_name,
                    'departments' => $course->departments->pluck('name')->all(),
                    'academic_level' => $course->academicLevel->name ?? 'N/A',
                    'credit_units' => $course->credit_units,
                ];
            });

        return response()->json(['success' => true, 'courses' => $courses]);
    }
}
