<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HodLecturerManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:hod', 'hod.role']);
    }

    /**
     * Display lecturer management page
     */
    public function index(Request $request)
    {
        $hod = Auth::guard('hod')->user();
        $departments = Department::orderBy('name')->get(['id', 'name']);

        return view('hod.management.lecturers.index', compact('departments'));
    }

    /**
     * Get lecturers with pagination and filters
     */
    public function getLecturers(Request $request)
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $query = Lecturer::with(['user:id,full_name,email', 'department:id,name'])
            ->where('department_id', $departmentId);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('staff_id', 'like', "%$search%")
                  ->orWhere('title', 'like', "%$search%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('full_name', 'like', "%$search%")
                               ->orWhere('email', 'like', "%$search%");
                  });
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $lecturers = $query->orderBy('staff_id')->paginate($request->per_page ?? 25);

        $transformedLecturers = $lecturers->getCollection()->map(function($lecturer) {
            return [
                'id' => $lecturer->id,
                'staff_id' => $lecturer->staff_id,
                'full_name' => $lecturer->user->full_name ?? 'N/A',
                'email' => $lecturer->user->email ?? 'N/A',
                'phone' => $lecturer->phone ?? 'N/A',
                'title' => $lecturer->title ?? 'N/A',
                'department' => $lecturer->department->name ?? 'N/A',
                'is_active' => $lecturer->is_active,
                'classrooms_count' => $lecturer->classrooms()->count(),
                'created_at' => $lecturer->created_at->format('Y-m-d H:i'),
            ];
        });

        $lecturers->setCollection($transformedLecturers);

        return response()->json([
            'success' => true,
            'data' => $lecturers,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
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
            'total' => Lecturer::where('department_id', $departmentId)->count(),
            'active' => Lecturer::where('department_id', $departmentId)->where('is_active', true)->count(),
            'inactive' => Lecturer::where('department_id', $departmentId)->where('is_active', false)->count(),
            'with_classes' => Lecturer::where('department_id', $departmentId)
                ->whereHas('classrooms')->count(),
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    /**
     * Bulk upload lecturers
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
                    $staffId = $row['staff id'] ?? $row['staff_id'] ?? null;
                    $fullName = $row['full name'] ?? $row['full_name'] ?? $row['name'] ?? null;
                    $email = $row['email'] ?? null;
                    $phone = $row['phone'] ?? $row['phone number'] ?? null;
                    $title = $row['title'] ?? null;

                    if (!$fullName || !$staffId) {
                        $errors[] = "Row " . ($i + 2) . ": Missing required fields (Full Name and Staff ID)";
                        $skipped++;
                        continue;
                    }

                    // Check if lecturer already exists
                    if (Lecturer::where('staff_id', $staffId)->exists()) {
                        $errors[] = "Row " . ($i + 2) . ": Staff ID already exists ($staffId)";
                        $skipped++;
                        continue;
                    }

                    // Check if email is already taken
                    if ($email && User::where('email', $email)->exists()) {
                        $errors[] = "Row " . ($i + 2) . ": Email already exists ($email)";
                        $skipped++;
                        continue;
                    }

                    // Create user
                    $user = User::create([
                        'username' => $staffId,
                        'full_name' => $fullName,
                        'email' => $email ?? $staffId . '@nsuk.edu.ng',
                        'password' => bcrypt('password123'),
                        'role' => 'lecturer',
                        'is_active' => true,
                    ]);

                    // Create lecturer
                    Lecturer::create([
                        'user_id' => $user->id,
                        'staff_id' => $staffId,
                        'phone' => $phone,
                        'department_id' => $departmentId,
                        'title' => $title,
                        'is_active' => true,
                    ]);

                    $created++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($i + 2) . ": " . $e->getMessage();
                    $skipped++;
                    Log::error('Lecturer upload error', ['row' => $i + 2, 'error' => $e->getMessage()]);
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
     * Update lecturer
     */
    public function update(Request $request, $id)
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $lecturer = Lecturer::where('department_id', $departmentId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|unique:lecturers,staff_id,' . $id,
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'title' => 'nullable|string',
            'is_active' => 'boolean',
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
            if ($lecturer->user) {
                $lecturer->user->update([
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                ]);
            }

            // Update lecturer
            $lecturer->update([
                'staff_id' => $request->staff_id,
                'phone' => $request->phone,
                'title' => $request->title,
                'is_active' => $request->is_active ?? $lecturer->is_active,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lecturer updated successfully',
                'data' => $lecturer->load(['user', 'department'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update lecturer error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete lecturer
     */
    public function destroy($id)
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $lecturer = Lecturer::where('department_id', $departmentId)->findOrFail($id);

        try {
            DB::beginTransaction();

            if ($lecturer->user) {
                $lecturer->user->delete();
            }

            $lecturer->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lecturer deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete lecturer error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show lecturer details (API)
     */
    public function show($id)
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $lecturer = Lecturer::with(['user:id,full_name,email', 'department:id,name'])
            ->where('department_id', $departmentId)
            ->findOrFail($id);

        $transformedLecturer = [
            'id' => $lecturer->id,
            'staff_id' => $lecturer->staff_id,
            'full_name' => $lecturer->user->full_name ?? 'N/A',
            'email' => $lecturer->user->email ?? 'N/A',
            'phone' => $lecturer->phone ?? 'N/A',
            'title' => $lecturer->title ?? 'N/A',
            'department' => $lecturer->department->name ?? 'N/A',
            'is_active' => $lecturer->is_active,
            'classrooms_count' => $lecturer->classrooms()->count(),
            'created_at' => $lecturer->created_at->format('Y-m-d H:i'),
            'updated_at' => $lecturer->updated_at->format('Y-m-d H:i'),
        ];

        return response()->json(['success' => true, 'data' => $transformedLecturer]);
    }

    /**
     * Display advanced lecturer details page
     */
    public function showDetails($id)
    {
        $hod = Auth::guard('hod')->user();
        $departmentId = $hod->department_id;

        $lecturer = Lecturer::with([
            'user:id,full_name,email',
            'department:id,name',
            'classrooms' => function($query) {
                $query->with([
                    'course:id,course_name,course_code',
                    'students:id'
                ])->where('is_active', true);
            }
        ])
        ->where('department_id', $departmentId)
        ->findOrFail($id);

        // Get teaching statistics
        $teachingStats = DB::table('classrooms')
            ->where('lecturer_id', $lecturer->id)
            ->selectRaw('
                COUNT(*) as total_classes,
                COALESCE(SUM((SELECT COUNT(*) FROM class_student WHERE class_student.classroom_id = classrooms.id)), 0) as total_students
            ')
            ->first();

        return view('hod.management.lecturers.show', [
            'lecturer' => $lecturer,
            'teachingStats' => $teachingStats,
        ]);
    }

    /**
     * Download template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="lecturers_template.csv"',
        ];

        $rows = [
            ['Staff ID', 'Full Name', 'Email', 'Phone', 'Title'],
            ['LEC001', 'Dr. Jane Smith', 'jane.smith@nsuk.edu.ng', '08012345678', 'Senior Lecturer'],
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

