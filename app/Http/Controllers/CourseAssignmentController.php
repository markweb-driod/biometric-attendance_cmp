<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CourseAssignmentController extends Controller
{
    /**
     * Display course assignment index page
     */
    public function index(Request $request)
    {
        // Check if superadmin or HOD
        $user = Auth::guard('superadmin')->user();
        $hod = Auth::guard('hod')->user();
        
        if (!$user && !$hod) {
            abort(403, 'Unauthorized access');
        }

        $departmentId = $hod ? $hod->department_id : null;
        
        // Get departments (all for superadmin, own for HOD)
        $departments = $departmentId 
            ? Department::where('id', $departmentId)->get()
            : Department::where('is_active', true)->orderBy('name')->get();

        // Determine which view to use based on auth guard
        if ($hod) {
            return view('hod.management.course-assignment.index', compact('departments'));
        } else {
            return view('superadmin.course-assignment', compact('departments'));
        }
    }

    /**
     * Get lecturers for assignment (API)
     */
    public function getLecturers(Request $request)
    {
        $user = Auth::guard('superadmin')->user();
        $hod = Auth::guard('hod')->user();
        
        if (!$user && !$hod) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $departmentId = $request->input('department_id');
        
        // For HOD, only show lecturers from their department
        // Only check if department_id is explicitly provided and different from HOD's department
        if ($hod && $departmentId !== null && $departmentId !== '' && $departmentId != $hod->department_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = Lecturer::with(['user:id,full_name,email', 'department:id,name', 'courses'])
            ->where('is_active', true);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        } elseif ($hod) {
            $query->where('department_id', $hod->department_id);
        }

        $lecturers = $query->orderBy('staff_id')->get()->map(function($lecturer) {
            return [
                'id' => $lecturer->id,
                'staff_id' => $lecturer->staff_id,
                'name' => $lecturer->user->full_name ?? 'N/A',
                'email' => $lecturer->user->email ?? 'N/A',
                'department' => $lecturer->department->name ?? 'N/A',
                'title' => $lecturer->title,
                'assigned_courses_count' => $lecturer->courses()->count(),
            ];
        });

        return response()->json(['success' => true, 'lecturers' => $lecturers]);
    }

    /**
     * Get dropdown data (departments, academic levels, semesters)
     */
    public function getDropdownData(Request $request)
    {
        $user = Auth::guard('superadmin')->user();
        $hod = Auth::guard('hod')->user();
        
        if (!$user && !$hod) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $departmentId = $hod ? $hod->department_id : null;

        $departments = $departmentId 
            ? Department::where('id', $departmentId)->get(['id', 'name', 'code'])
            : Department::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);

        $academicLevels = \App\Models\AcademicLevel::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $semesters = \App\Models\Semester::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json([
            'success' => true,
            'departments' => $departments,
            'academic_levels' => $academicLevels,
            'semesters' => $semesters,
        ]);
    }

    /**
     * Create a new course
     */
    public function createCourse(Request $request)
    {
        $user = Auth::guard('superadmin')->user();
        $hod = Auth::guard('hod')->user();
        
        if (!$user && !$hod) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'course_code' => 'required|string|max:20|unique:courses',
            'course_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credit_units' => 'required|integer|min:1|max:10',
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => 'exists:departments,id',
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

        // For HOD, verify all selected departments match their department
        if ($hod) {
            $invalid = collect($request->department_ids)->contains(function($deptId) use ($hod) {
                return (int)$deptId !== (int)$hod->department_id;
            });
            if ($invalid) {
                return response()->json(['error' => 'You can only create courses for your department'], 403);
            }
        }

        try {
            $course = Course::create([
                'course_code' => strtoupper($request->course_code),
                'course_name' => $request->course_name,
                'description' => $request->description,
                'credit_units' => $request->credit_units,
                'academic_level_id' => $request->academic_level_id,
                'semester_id' => $request->semester_id,
                'is_active' => $request->is_active ?? true,
            ]);

            // Attach departments
            $course->departments()->sync($request->department_ids);

            Log::info('Course created via assignment page', [
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'created_by' => $user ? 'superadmin' : 'hod',
                'created_by_id' => $user ? $user->id : $hod->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course created successfully',
                'data' => $course->load(['departments:id,name', 'academicLevel:id,name', 'semester:id,name'])
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
     * Get courses for assignment (API)
     */
    public function getCourses(Request $request)
    {
        $user = Auth::guard('superadmin')->user();
        $hod = Auth::guard('hod')->user();
        
        if (!$user && !$hod) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $departmentId = $request->input('department_id');
        
        if ($hod && $departmentId !== null && $departmentId !== '' && $departmentId != $hod->department_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = Course::with(['departments:id,name', 'academicLevel:id,name', 'lecturers'])
            ->where('is_active', true);

        if ($departmentId) {
            $query->whereHas('departments', function($q) use ($departmentId) {
                $q->where('departments.id', $departmentId);
            });
        } elseif ($hod) {
            $query->whereHas('departments', function($q) use ($hod) {
                $q->where('departments.id', $hod->department_id);
            });
        }

        $courses = $query->orderBy('course_code')->get()->map(function($course) {
            return [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'departments' => $course->departments->pluck('name')->all(),
                'academic_level' => $course->academicLevel->name ?? 'N/A',
                'credit_units' => $course->credit_units,
                'assigned_lecturers_count' => $course->lecturers()->count(),
            ];
        });

        return response()->json(['success' => true, 'courses' => $courses]);
    }

    /**
     * Get assigned courses for a lecturer (API)
     */
    public function getLecturerCourses(Request $request, $lecturerId)
    {
        $user = Auth::guard('superadmin')->user();
        $hod = Auth::guard('hod')->user();
        
        if (!$user && !$hod) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $lecturer = Lecturer::findOrFail($lecturerId);

        if ($hod && $lecturer->department_id != $hod->department_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $courses = $lecturer->allCourses()
            ->with(['departments:id,name', 'academicLevel:id,name'])
            ->get()
            ->map(function($course) {
                // Get assigned_by information
                $assignedByName = 'N/A';
                $assignedByRole = null;
                
                if ($course->pivot->assigned_by_type && $course->pivot->assigned_by_id) {
                    try {
                        if ($course->pivot->assigned_by_type === 'App\\Models\\Superadmin') {
                            $assigner = \App\Models\Superadmin::find($course->pivot->assigned_by_id);
                            if ($assigner) {
                                $assignedByName = $assigner->full_name ?? 'Superadmin #' . $assigner->id;
                                $assignedByRole = 'Superadmin';
                            }
                        } elseif ($course->pivot->assigned_by_type === 'App\\Models\\Hod') {
                            $assigner = \App\Models\Hod::with('user:id,full_name')->find($course->pivot->assigned_by_id);
                            if ($assigner) {
                                $assignedByName = $assigner->user->full_name ?? 'HOD #' . $assigner->id;
                                $assignedByRole = 'HOD';
                            }
                        }
                    } catch (\Exception $e) {
                        // Silently fail if assigner not found
                    }
                }
                
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'course_name' => $course->course_name,
                    'departments' => $course->departments->pluck('name')->all(),
                    'academic_level' => $course->academicLevel->name ?? 'N/A',
                    'credit_units' => $course->credit_units,
                    'is_active' => $course->pivot->is_active,
                    'assigned_at' => $course->pivot->assigned_at,
                    'assigned_by_name' => $assignedByName,
                    'assigned_by_role' => $assignedByRole,
                ];
            });

        return response()->json(['success' => true, 'courses' => $courses]);
    }

    /**
     * Get lecturers assigned to a course (API)
     */
    public function getCourseLecturers(Request $request, $courseId)
    {
        $user = Auth::guard('superadmin')->user();
        $hod = Auth::guard('hod')->user();
        
        if (!$user && !$hod) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $course = Course::findOrFail($courseId);

        // HOD can access only if the course is linked to their department
        if ($hod && !$course->departments()->where('departments.id', $hod->department_id)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $lecturers = $course->allLecturers()
            ->with(['user:id,full_name,email', 'department:id,name'])
            ->get()
            ->map(function($lecturer) {
                return [
                    'id' => $lecturer->id,
                    'staff_id' => $lecturer->staff_id,
                    'name' => $lecturer->user->full_name ?? 'N/A',
                    'email' => $lecturer->user->email ?? 'N/A',
                    'department' => $lecturer->department->name ?? 'N/A',
                    'title' => $lecturer->title,
                    'is_active' => $lecturer->pivot->is_active,
                    'assigned_at' => $lecturer->pivot->assigned_at,
                ];
            });

        return response()->json(['success' => true, 'lecturers' => $lecturers]);
    }

    /**
     * Assign courses to a lecturer
     */
    public function assignCourses(Request $request)
    {
        $user = Auth::guard('superadmin')->user();
        $hod = Auth::guard('hod')->user();
        
        if (!$user && !$hod) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'lecturer_id' => 'required|exists:lecturers,id',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $lecturer = Lecturer::findOrFail($request->lecturer_id);

        // For HOD, verify lecturer belongs to their department
        if ($hod && $lecturer->department_id != $hod->department_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify all courses belong to same department as lecturer (or superadmin)
        $courseIds = $request->course_ids;
        if ($hod) {
            $invalidCourses = Course::whereIn('id', $courseIds)
                ->whereDoesntHave('departments', function($q) use ($lecturer) {
                    $q->where('departments.id', $lecturer->department_id);
                })
                ->exists();
            
            if ($invalidCourses) {
                return response()->json(['error' => 'Cannot assign courses from different department'], 422);
            }
        }

        DB::beginTransaction();
        try {
            $assigned = [];
            $alreadyAssigned = [];

            foreach ($courseIds as $courseId) {
                $course = Course::find($courseId);
                
                // Check if already assigned and active
                $existing = DB::table('lecturer_course')
                    ->where('lecturer_id', $lecturer->id)
                    ->where('course_id', $courseId)
                    ->first();

                if ($existing && $existing->is_active) {
                    $alreadyAssigned[] = $course->course_code;
                    continue;
                }

                // Assign or reactivate
                $assignedByType = $user ? 'App\\Models\\Superadmin' : 'App\\Models\\Hod';
                $assignedById = $user ? $user->id : $hod->id;
                
                DB::table('lecturer_course')->updateOrInsert(
                    [
                        'lecturer_id' => $lecturer->id,
                        'course_id' => $courseId,
                    ],
                    [
                        'is_active' => true,
                        'assigned_at' => now(),
                        'unassigned_at' => null,
                        'assigned_by_type' => $assignedByType,
                        'assigned_by_id' => $assignedById,
                        'updated_at' => now(),
                    ]
                );

                $assigned[] = $course->course_code;

                // Log activity
                Log::info('Course assigned to lecturer', [
                    'lecturer_id' => $lecturer->id,
                    'course_id' => $courseId,
                    'assigned_by' => $user ? 'superadmin' : 'hod',
                    'assigned_by_id' => $user ? $user->id : $hod->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($assigned) . ' course(s) assigned successfully',
                'assigned' => $assigned,
                'already_assigned' => $alreadyAssigned,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning courses to lecturer', [
                'error' => $e->getMessage(),
                'lecturer_id' => $lecturer->id,
                'course_ids' => $courseIds,
            ]);

            return response()->json(['error' => 'Failed to assign courses: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Unassign courses from a lecturer
     */
    public function unassignCourses(Request $request)
    {
        $user = Auth::guard('superadmin')->user();
        $hod = Auth::guard('hod')->user();
        
        if (!$user && !$hod) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'lecturer_id' => 'required|exists:lecturers,id',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $lecturer = Lecturer::findOrFail($request->lecturer_id);

        // For HOD, verify lecturer belongs to their department
        if ($hod && $lecturer->department_id != $hod->department_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            $unassigned = [];

            foreach ($request->course_ids as $courseId) {
                $course = Course::find($courseId);
                
                // Deactivate assignment
                DB::table('lecturer_course')
                    ->where('lecturer_id', $lecturer->id)
                    ->where('course_id', $courseId)
                    ->update([
                        'is_active' => false,
                        'unassigned_at' => now(),
                        'updated_at' => now(),
                    ]);

                $unassigned[] = $course->course_code;

                // Log activity
                Log::info('Course unassigned from lecturer', [
                    'lecturer_id' => $lecturer->id,
                    'course_id' => $courseId,
                    'unassigned_by' => $user ? 'superadmin' : 'hod',
                    'unassigned_by_id' => $user ? $user->id : $hod->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($unassigned) . ' course(s) unassigned successfully',
                'unassigned' => $unassigned,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error unassigning courses from lecturer', [
                'error' => $e->getMessage(),
                'lecturer_id' => $lecturer->id,
                'course_ids' => $request->course_ids,
            ]);

            return response()->json(['error' => 'Failed to unassign courses: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk assign courses to multiple lecturers
     */
    public function bulkAssign(Request $request)
    {
        $user = Auth::guard('superadmin')->user();
        $hod = Auth::guard('hod')->user();
        
        if (!$user && !$hod) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'lecturer_ids' => 'required|array',
            'lecturer_ids.*' => 'exists:lecturers,id',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $totalAssigned = 0;
            $results = [];

            foreach ($request->lecturer_ids as $lecturerId) {
                $lecturer = Lecturer::find($lecturerId);

                // For HOD, verify lecturer belongs to their department
                if ($hod && $lecturer->department_id != $hod->department_id) {
                    continue;
                }

                $assigned = [];
                foreach ($request->course_ids as $courseId) {
                    $course = Course::find($courseId);

                    // For HOD, verify course belongs to same department
                    if ($hod && $course->department_id != $lecturer->department_id) {
                        continue;
                    }

                    // Check if already assigned and active
                    $existing = DB::table('lecturer_course')
                        ->where('lecturer_id', $lecturer->id)
                        ->where('course_id', $courseId)
                        ->where('is_active', true)
                        ->exists();

                    if ($existing) {
                        continue;
                    }

                    // Assign or reactivate
                    $assignedByType = $user ? 'App\\Models\\Superadmin' : 'App\\Models\\Hod';
                    $assignedById = $user ? $user->id : $hod->id;
                    
                    DB::table('lecturer_course')->updateOrInsert(
                        [
                            'lecturer_id' => $lecturer->id,
                            'course_id' => $courseId,
                        ],
                        [
                            'is_active' => true,
                            'assigned_at' => now(),
                            'unassigned_at' => null,
                            'assigned_by_type' => $assignedByType,
                            'assigned_by_id' => $assignedById,
                            'updated_at' => now(),
                        ]
                    );

                    $assigned[] = $course->course_code;
                    $totalAssigned++;
                }

                if (!empty($assigned)) {
                    $results[$lecturer->staff_id] = $assigned;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Assigned courses to {$totalAssigned} lecturer-course combinations",
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk course assignment', [
                'error' => $e->getMessage(),
                'lecturer_ids' => $request->lecturer_ids,
                'course_ids' => $request->course_ids,
            ]);

            return response()->json(['error' => 'Failed to assign courses: ' . $e->getMessage()], 500);
        }
    }
}
