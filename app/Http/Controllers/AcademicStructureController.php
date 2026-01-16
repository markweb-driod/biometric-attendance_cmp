<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Course;
use App\Models\AcademicLevel;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademicStructureController extends Controller
{
    /**
     * Display academic structure management dashboard
     */
    public function index()
    {
        $stats = $this->getAcademicStats();
        $recentActivity = $this->getRecentActivity();
        
        return view('superadmin.academic-structure', compact('stats', 'recentActivity'));
    }

    /**
     * Get academic structure statistics
     */
    private function getAcademicStats()
    {
        return Cache::remember('academic_stats', 300, function () {
            return [
                'departments' => Department::where('is_active', true)->count(),
                'courses' => Course::where('is_active', true)->count(),
                'academic_levels' => AcademicLevel::where('is_active', true)->count(),
                'classrooms' => Classroom::where('is_active', true)->count(),
                'total_students' => \App\Models\Student::where('is_active', true)->count(),
                'total_lecturers' => \App\Models\Lecturer::where('is_active', true)->count(),
            ];
        });
    }

    /**
     * Get recent academic activity
     */
    private function getRecentActivity()
    {
        return Cache::remember('academic_recent_activity', 600, function () {
            $activities = [];
            
            // Recent departments
            $recentDepartments = Department::select(['id', 'name', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
                
            foreach ($recentDepartments as $dept) {
                if ($dept->created_at) {
                    $activities[] = [
                        'type' => 'department_created',
                        'message' => "Department '{$dept->name}' created",
                        'time' => $dept->created_at->diffForHumans(),
                        'icon' => 'building',
                        'color' => 'blue'
                    ];
                }
            }
            
            // Recent courses
            $recentCourses = Course::select(['id', 'course_name', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
                
            foreach ($recentCourses as $course) {
                if ($course->created_at) {
                    $activities[] = [
                        'type' => 'course_created',
                        'message' => "Course '{$course->course_name}' created",
                        'time' => $course->created_at->diffForHumans(),
                        'icon' => 'book',
                        'color' => 'green'
                    ];
                }
            }
            
            // Recent classrooms
            $recentClassrooms = Classroom::select(['id', 'class_name', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
                
            foreach ($recentClassrooms as $classroom) {
                if ($classroom->created_at) {
                    $activities[] = [
                        'type' => 'classroom_created',
                        'message' => "Classroom '{$classroom->class_name}' created",
                        'time' => $classroom->created_at->diffForHumans(),
                        'icon' => 'chalkboard',
                        'color' => 'purple'
                    ];
                }
            }
            
            // Sort by time and return latest 8
            usort($activities, function($a, $b) {
                return strtotime($b['time']) - strtotime($a['time']);
            });
            
            return array_slice($activities, 0, 8);
        });
    }

    // ==================== DEPARTMENTS ====================

    /**
     * Get all departments
     */
    public function getDepartments(Request $request)
    {
        $query = Department::select(['id', 'name', 'code', 'description', 'is_active', 'created_at']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $departments = $query->orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    /**
     * Create department
     */
    public function createDepartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments',
            'code' => 'required|string|max:10|unique:departments',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $department = Department::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
            ]);

            Cache::forget('academic_stats');
            Cache::forget('academic_recent_activity');
            Cache::forget('departments_list');

            Log::info('Department created', [
                'department_id' => $department->id,
                'name' => $department->name,
                'created_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Department created successfully',
                'data' => $department
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create department', [
                'error' => $e->getMessage(),
                'name' => $request->name
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create department: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update department
     */
    public function updateDepartment(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'code' => 'required|string|max:10|unique:departments,code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $department->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'is_active' => $request->is_active ?? $department->is_active,
            ]);

            Cache::forget('academic_stats');
            Cache::forget('departments_list');

            Log::info('Department updated', [
                'department_id' => $department->id,
                'name' => $department->name,
                'updated_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Department updated successfully',
                'data' => $department
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update department', [
                'department_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update department: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete department
     */
    public function deleteDepartment($id)
    {
        $department = Department::findOrFail($id);

        try {
            // Check if department has students or lecturers
            $studentCount = $department->students()->count();
            $lecturerCount = $department->lecturers()->count();
            $courseCount = $department->courses()->count();

            if ($studentCount > 0 || $lecturerCount > 0 || $courseCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete department. It has {$studentCount} students, {$lecturerCount} lecturers, and {$courseCount} courses."
                ], 400);
            }

            $department->delete();

            Cache::forget('academic_stats');
            Cache::forget('departments_list');

            Log::info('Department deleted', [
                'department_id' => $id,
                'name' => $department->name,
                'deleted_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Department deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete department', [
                'department_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete department: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== ACADEMIC LEVELS ====================

    /**
     * Get all academic levels
     */
    public function getAcademicLevels(Request $request)
    {
        $query = AcademicLevel::select(['id', 'name', 'level_number', 'description', 'is_active', 'created_at']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $levels = $query->orderBy('level_number')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $levels
        ]);
    }

    /**
     * Create academic level
     */
    public function createAcademicLevel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:academic_levels',
            'level_number' => 'required|integer|min:1|max:10|unique:academic_levels',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $level = AcademicLevel::create([
                'name' => $request->name,
                'level_number' => $request->level_number,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
            ]);

            Cache::forget('academic_stats');
            Cache::forget('academic_levels_list');

            Log::info('Academic level created', [
                'level_id' => $level->id,
                'name' => $level->name,
                'created_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Academic level created successfully',
                'data' => $level
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create academic level', [
                'error' => $e->getMessage(),
                'name' => $request->name
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create academic level: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update academic level
     */
    public function updateAcademicLevel(Request $request, $id)
    {
        $level = AcademicLevel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:academic_levels,name,' . $id,
            'level_number' => 'required|integer|min:1|max:10|unique:academic_levels,level_number,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $level->update([
                'name' => $request->name,
                'level_number' => $request->level_number,
                'description' => $request->description,
                'is_active' => $request->is_active ?? $level->is_active,
            ]);

            Cache::forget('academic_levels_list');

            Log::info('Academic level updated', [
                'level_id' => $level->id,
                'name' => $level->name,
                'updated_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Academic level updated successfully',
                'data' => $level
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update academic level', [
                'level_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update academic level: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete academic level
     */
    public function deleteAcademicLevel($id)
    {
        $level = AcademicLevel::findOrFail($id);

        try {
            // Check if level has students or courses
            $studentCount = $level->students()->count();
            $courseCount = $level->courses()->count();

            if ($studentCount > 0 || $courseCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete academic level. It has {$studentCount} students and {$courseCount} courses."
                ], 400);
            }

            $level->delete();

            Cache::forget('academic_stats');
            Cache::forget('academic_levels_list');

            Log::info('Academic level deleted', [
                'level_id' => $id,
                'name' => $level->name,
                'deleted_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Academic level deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete academic level', [
                'level_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete academic level: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== COURSES ====================

    /**
     * Get all courses
     */
    public function getCourses(Request $request)
    {
        $query = Course::with(['department:id,name', 'academicLevel:id,name'])
            ->select(['id', 'course_code', 'course_name', 'description', 'credit_units', 'department_id', 'academic_level_id', 'is_active', 'created_at']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('course_code', 'like', "%$search%")
                  ->orWhere('course_name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('academic_level_id')) {
            $query->where('academic_level_id', $request->academic_level_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $courses = $query->orderBy('course_code')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    /**
     * Create course
     */
    public function createCourse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_code' => 'required|string|max:20|unique:courses',
            'course_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credit_units' => 'required|integer|min:1|max:10',
            'department_id' => 'required|exists:departments,id',
            'academic_level_id' => 'required|exists:academic_levels,id',
            'semester_id' => 'required|exists:semesters,id',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $course = Course::create([
                'course_code' => strtoupper($request->course_code),
                'course_name' => $request->course_name,
                'description' => $request->description,
                'credit_units' => $request->credit_units,
                'department_id' => $request->department_id,
                'academic_level_id' => $request->academic_level_id,
                'semester_id' => $request->semester_id,
                'is_active' => $request->is_active ?? true,
            ]);

            Cache::forget('academic_stats');
            Cache::forget('academic_recent_activity');
            Cache::forget('courses_list');

            Log::info('Course created', [
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'created_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course created successfully',
                'data' => $course->load(['department:id,name', 'academicLevel:id,name', 'semester:id,name,code'])
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create course', [
                'error' => $e->getMessage(),
                'course_code' => $request->course_code
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create course: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update course
     */
    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $id,
            'course_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credit_units' => 'required|integer|min:1|max:10',
            'department_id' => 'required|exists:departments,id',
            'academic_level_id' => 'required|exists:academic_levels,id',
            'semester_id' => 'required|exists:semesters,id',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $course->update([
                'course_code' => strtoupper($request->course_code),
                'course_name' => $request->course_name,
                'description' => $request->description,
                'credit_units' => $request->credit_units,
                'department_id' => $request->department_id,
                'academic_level_id' => $request->academic_level_id,
                'semester_id' => $request->semester_id,
                'is_active' => $request->is_active ?? $course->is_active,
            ]);

            Cache::forget('courses_list');

            Log::info('Course updated', [
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'updated_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course updated successfully',
                'data' => $course->load(['department:id,name', 'academicLevel:id,name'])
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update course', [
                'course_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update course: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete course
     */
    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);

        try {
            // Check if course has classrooms
            $classroomCount = $course->classrooms()->count();

            if ($classroomCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete course. It has {$classroomCount} classrooms."
                ], 400);
            }

            $course->delete();

            Cache::forget('academic_stats');
            Cache::forget('courses_list');

            Log::info('Course deleted', [
                'course_id' => $id,
                'course_code' => $course->course_code,
                'deleted_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete course', [
                'course_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete course: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== CLASSROOMS ====================

    /**
     * Get all classrooms
     */
    public function getClassrooms(Request $request)
    {
        $query = Classroom::with(['lecturer.user:id,full_name', 'course:id,course_name,course_code'])
            ->select(['id', 'class_name', 'course_id', 'lecturer_id', 'pin', 'schedule', 'description', 'is_active', 'created_at']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('class_name', 'like', "%$search%")
                  ->orWhere('pin', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('lecturer_id')) {
            $query->where('lecturer_id', $request->lecturer_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $classrooms = $query->orderBy('class_name')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $classrooms
        ]);
    }

    /**
     * Create classroom
     */
    public function createClassroom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'pin' => 'required|string|max:20|unique:classrooms',
            'schedule' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $classroom = Classroom::create([
                'class_name' => $request->class_name,
                'course_id' => $request->course_id,
                'lecturer_id' => $request->lecturer_id,
                'pin' => strtoupper($request->pin),
                'schedule' => $request->schedule,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
            ]);

            Cache::forget('academic_stats');
            Cache::forget('academic_recent_activity');

            Log::info('Classroom created', [
                'classroom_id' => $classroom->id,
                'class_name' => $classroom->class_name,
                'created_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Classroom created successfully',
                'data' => $classroom->load(['lecturer.user:id,full_name', 'course:id,course_name,course_code'])
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create classroom', [
                'error' => $e->getMessage(),
                'class_name' => $request->class_name
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create classroom: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update classroom
     */
    public function updateClassroom(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'class_name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'pin' => 'required|string|max:20|unique:classrooms,pin,' . $id,
            'schedule' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $classroom->update([
                'class_name' => $request->class_name,
                'course_id' => $request->course_id,
                'lecturer_id' => $request->lecturer_id,
                'pin' => strtoupper($request->pin),
                'schedule' => $request->schedule,
                'description' => $request->description,
                'is_active' => $request->is_active ?? $classroom->is_active,
            ]);

            Log::info('Classroom updated', [
                'classroom_id' => $classroom->id,
                'class_name' => $classroom->class_name,
                'updated_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Classroom updated successfully',
                'data' => $classroom->load(['lecturer.user:id,full_name', 'course:id,course_name,course_code'])
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update classroom', [
                'classroom_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update classroom: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete classroom
     */
    public function deleteClassroom($id)
    {
        $classroom = Classroom::findOrFail($id);

        try {
            // Check if classroom has students or attendance sessions
            $studentCount = $classroom->students()->count();
            $sessionCount = $classroom->attendanceSessions()->count();

            if ($studentCount > 0 || $sessionCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete classroom. It has {$studentCount} students and {$sessionCount} attendance sessions."
                ], 400);
            }

            $classroom->delete();

            Cache::forget('academic_stats');

            Log::info('Classroom deleted', [
                'classroom_id' => $id,
                'class_name' => $classroom->class_name,
                'deleted_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Classroom deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete classroom', [
                'classroom_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete classroom: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dropdown data for forms
     */
    public function getDropdownData()
    {
        $departments = Department::where('is_active', true)
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        $academicLevels = AcademicLevel::where('is_active', true)
            ->select(['id', 'name', 'level_number'])
            ->orderBy('level_number')
            ->get();

        $courses = Course::where('is_active', true)
            ->select(['id', 'course_name', 'course_code'])
            ->orderBy('course_code')
            ->get();

        $lecturers = \App\Models\Lecturer::with('user:id,full_name')
            ->where('is_active', true)
            ->select(['id', 'user_id', 'staff_id'])
            ->orderBy('staff_id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'departments' => $departments,
                'academic_levels' => $academicLevels,
                'courses' => $courses,
                'lecturers' => $lecturers
            ]
        ]);
    }
}
