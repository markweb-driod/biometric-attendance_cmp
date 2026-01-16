<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\AcademicLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HodStudentManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:hod', 'hod.role']);
    }

    /**
     * Display student management page
     */
    public function index(Request $request)
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        // Get filter options
        $levels = AcademicLevel::orderBy('name')->get(['id', 'name']);
        $departments = Department::orderBy('name')->get(['id', 'name']);

        return view('hod.management.students.index', compact('levels', 'departments'));
    }

    /**
     * Get students with pagination and filters
     */
    public function getStudents(Request $request)
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $query = Student::with(['user:id,full_name,email', 'department:id,name', 'academicLevel:id,name'])
            ->where('department_id', $departmentId);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('matric_number', 'like', "%$search%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('full_name', 'like', "%$search%")
                               ->orWhere('email', 'like', "%$search%");
                  });
            });
        }

        // Apply level filter
        if ($request->filled('level_id')) {
            $query->where('academic_level_id', $request->level_id);
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Apply face registration filter
        if ($request->filled('face_registration')) {
            if ($request->face_registration === 'enabled') {
                $query->where('face_registration_enabled', true);
            } elseif ($request->face_registration === 'disabled') {
                $query->where('face_registration_enabled', false);
            }
        }

        $students = $query->orderBy('matric_number')->paginate($request->per_page ?? 25);

        $transformedStudents = $students->getCollection()->map(function($student) {
            return [
                'id' => $student->id,
                'matric_number' => $student->matric_number,
                'full_name' => $student->user->full_name ?? 'N/A',
                'email' => $student->user->email ?? 'N/A',
                'phone' => $student->phone ?? 'N/A',
                'academic_level' => $student->academicLevel->name ?? 'N/A',
                'academic_level_id' => $student->academic_level_id,
                'department' => $student->department->name ?? 'N/A',
                'is_active' => $student->is_active,
                'face_registration_enabled' => $student->face_registration_enabled,
                'created_at' => $student->created_at->format('Y-m-d H:i'),
            ];
        });

        $students->setCollection($transformedStudents);

        return response()->json([
            'success' => true,
            'data' => $students,
            'filters' => [
                'search' => $request->search,
                'level_id' => $request->level_id,
                'status' => $request->status,
                'face_registration' => $request->face_registration,
            ]
        ]);
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $stats = [
            'total' => Student::where('department_id', $departmentId)->count(),
            'active' => Student::where('department_id', $departmentId)->where('is_active', true)->count(),
            'inactive' => Student::where('department_id', $departmentId)->where('is_active', false)->count(),
            'face_registered' => Student::where('department_id', $departmentId)
                ->where('face_registration_enabled', true)->count(),
            'not_face_registered' => Student::where('department_id', $departmentId)
                ->where('face_registration_enabled', false)->count(),
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    /**
     * Bulk upload students
     */
    public function bulkUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file',
                'errors' => $validator->errors()
            ], 422);
        }

        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $rows = [];
        $errors = [];
        $created = 0;
        $skipped = 0;

        try {
            if (in_array($extension, ['csv', 'txt'])) {
                $handle = fopen($file->getRealPath(), 'r');
                if (!$handle) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to read CSV file'
                    ], 422);
                }
                
                $header = fgetcsv($handle);
                if (!$header) {
                    fclose($handle);
                    return response()->json([
                        'success' => false,
                        'message' => 'CSV file is empty'
                    ], 422);
                }
                
                $header = array_map('strtolower', $header);
                $header = array_map('trim', $header);
                
                while (($data = fgetcsv($handle)) !== false) {
                    $row = array_combine($header, $data);
                    $rows[] = $row;
                }
                fclose($handle);
            } elseif (in_array($extension, ['xlsx', 'xls'])) {
                $imported = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);
                $sheet = $imported[0] ?? [];
                
                if (count($sheet) < 2) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Excel file is empty or missing header'
                    ], 422);
                }
                
                $header = array_map('strtolower', $sheet[0]);
                $header = array_map('trim', $header);
                
                for ($i = 1; $i < count($sheet); $i++) {
                    $row = array_combine($header, $sheet[$i]);
                    $rows[] = $row;
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unsupported file type'
                ], 422);
            }

            DB::beginTransaction();

            foreach ($rows as $i => $row) {
                try {
                    // Normalize column names
                    $matricNumber = $row['matric number'] ?? $row['matric_number'] ?? null;
                    $fullName = $row['full name'] ?? $row['full_name'] ?? $row['name'] ?? null;
                    $email = $row['email'] ?? null;
                    $phone = $row['phone'] ?? $row['phone number'] ?? null;
                    $levelName = $row['level'] ?? $row['academic level'] ?? $row['academic_level'] ?? null;

                    if (!$fullName || !$matricNumber) {
                        $errors[] = "Row " . ($i + 2) . ": Missing required fields (Full Name and Matric Number)";
                        $skipped++;
                        continue;
                    }

                    // Check if student already exists
                    if (Student::where('matric_number', $matricNumber)->exists()) {
                        $errors[] = "Row " . ($i + 2) . ": Matric number already exists ($matricNumber)";
                        $skipped++;
                        continue;
                    }

                    // Find academic level
                    $academicLevel = null;
                    if ($levelName) {
                        $academicLevel = AcademicLevel::where('name', 'like', "%$levelName%")->first();
                    }

                    if (!$academicLevel) {
                        $errors[] = "Row " . ($i + 2) . ": Invalid academic level ($levelName)";
                        $skipped++;
                        continue;
                    }

                    // Create user
                    $user = User::create([
                        'username' => $matricNumber,
                        'full_name' => $fullName,
                        'email' => $email ?? $matricNumber . '@student.nsuk.edu.ng',
                        'password' => bcrypt('password123'),
                        'role' => 'student',
                        'is_active' => true,
                    ]);

                    // Create student
                    Student::create([
                        'user_id' => $user->id,
                        'matric_number' => $matricNumber,
                        'phone' => $phone,
                        'department_id' => $departmentId,
                        'academic_level_id' => $academicLevel->id,
                        'is_active' => true,
                        'face_registration_enabled' => false,
                    ]);

                    $created++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($i + 2) . ": " . $e->getMessage();
                    $skipped++;
                    Log::error('Student upload error', ['row' => $i + 2, 'error' => $e->getMessage()]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Upload completed. Created: $created, Skipped: $skipped",
                'created' => $created,
                'skipped' => $skipped,
                'errors' => $errors,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk upload failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update student
     */
    public function update(Request $request, $id)
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $student = Student::where('department_id', $departmentId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'matric_number' => 'required|unique:students,matric_number,' . $id,
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'academic_level_id' => 'required|exists:academic_levels,id',
            'is_active' => 'boolean',
            'face_registration_enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update user
            if ($student->user) {
                $student->user->update([
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                ]);
            }

            // Update student
            $student->update([
                'matric_number' => $request->matric_number,
                'phone' => $request->phone,
                'academic_level_id' => $request->academic_level_id,
                'is_active' => $request->is_active ?? $student->is_active,
                'face_registration_enabled' => $request->face_registration_enabled ?? $student->face_registration_enabled,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully',
                'data' => $student->load(['user', 'department', 'academicLevel'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update student error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete student
     */
    public function destroy($id)
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $student = Student::where('department_id', $departmentId)->findOrFail($id);

        try {
            DB::beginTransaction();

            if ($student->user) {
                $student->user->delete();
            }

            $student->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete student error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show student details (API)
     */
    public function show($id)
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $student = Student::with(['user:id,full_name,email', 'department:id,name', 'academicLevel:id,name'])
            ->where('department_id', $departmentId)
            ->findOrFail($id);

        $transformedStudent = [
            'id' => $student->id,
            'matric_number' => $student->matric_number,
            'full_name' => $student->user->full_name ?? 'N/A',
            'email' => $student->user->email ?? 'N/A',
            'phone' => $student->phone ?? 'N/A',
            'academic_level' => $student->academicLevel->name ?? 'N/A',
            'academic_level_id' => $student->academic_level_id,
            'department' => $student->department->name ?? 'N/A',
            'is_active' => $student->is_active,
            'face_registration_enabled' => $student->face_registration_enabled,
            'reference_image_path' => $student->reference_image_path,
            'created_at' => $student->created_at->format('Y-m-d H:i'),
            'updated_at' => $student->updated_at->format('Y-m-d H:i'),
        ];

        return response()->json(['success' => true, 'data' => $transformedStudent]);
    }

    /**
     * Display advanced student details page
     */
    public function showDetails($id)
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $student = Student::with([
            'user:id,full_name,email',
            'department:id,name',
            'academicLevel:id,name',
            'classrooms' => function($query) {
                $query->with([
                    'course:id,course_name,course_code',
                    'lecturer.user:id,full_name'
                ]);
            }
        ])
        ->where('department_id', $departmentId)
        ->findOrFail($id);

        // Get attendance statistics
        $attendanceStats = DB::table('attendances')
            ->join('classrooms', 'attendances.classroom_id', '=', 'classrooms.id')
            ->where('attendances.student_id', $student->id)
            ->selectRaw('
                COUNT(*) as total_records,
                SUM(CASE WHEN attendances.status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN attendances.status = "absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN attendances.status = "late" THEN 1 ELSE 0 END) as late_count
            ')
            ->first();

        return view('hod.management.students.show', [
            'student' => $student,
            'attendanceStats' => $attendanceStats,
        ]);
    }

    /**
     * Download template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_template.csv"',
        ];

        $rows = [
            ['Full Name', 'Matric Number', 'Email', 'Phone', 'Level'],
            ['John Doe', 'NS/2020/001', 'john.doe@student.nsuk.edu.ng', '08012345678', '100 Level'],
        ];

        $callback = function() use ($rows) {
            $file = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

