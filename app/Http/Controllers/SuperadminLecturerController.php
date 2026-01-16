<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Lecturer;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\Log;

class SuperadminLecturerController extends Controller
{
    public function index(Request $request)
    {
        $query = Lecturer::with('user');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('staff_id', 'like', "%$search%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('full_name', 'like', "%$search%")
                               ->orWhere('email', 'like', "%$search%");
                  });
            });
        }
        if ($request->filled('department')) {
            $query->whereHas('department', function($deptQuery) use ($request) {
                $deptQuery->where('name', $request->department);
            });
        }
        $lecturers = $query->orderBy('staff_id')->get();
        return response()->json(['success' => true, 'data' => $lecturers]);
    }

    public function show($id)
    {
        $lecturer = Lecturer::with(['user:id,full_name,email', 'department:id,name'])
            ->select(['id', 'user_id', 'staff_id', 'phone', 'department_id', 'title', 'is_active', 'created_at'])
            ->findOrFail($id);
        return response()->json(['success' => true, 'data' => $lecturer]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|unique:lecturers',
            'name' => 'required',
            'email' => 'required|email|unique:lecturers',
            'department' => 'nullable',
            'title' => 'nullable',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $data = $request->only(['staff_id', 'name', 'email', 'department', 'title']);
        $data['password'] = bcrypt($request->password);
        $lecturer = Lecturer::create($data);
        return response()->json(['success' => true, 'data' => $lecturer]);
    }

    public function update(Request $request, $id)
    {
        $lecturer = Lecturer::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|unique:lecturers,staff_id,' . $id,
            'name' => 'required',
            'email' => 'required|email|unique:lecturers,email,' . $id,
            'department' => 'nullable',
            'title' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $lecturer->update($request->only(['staff_id', 'name', 'email', 'department', 'title']));
        return response()->json(['success' => true, 'data' => $lecturer]);
    }

    public function destroy($id)
    {
        $lecturer = Lecturer::findOrFail($id);
        $lecturer->delete();
        return response()->json(['success' => true]);
    }

    // Bulk upload (CSV/XLSX)
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file');
        $rows = [];
        $errors = [];
        $created = 0;

        if (in_array($file->getClientOriginalExtension(), ['csv', 'txt'])) {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle);
            $header = array_map('strtolower', $header);
            while (($data = fgetcsv($handle)) !== false) {
                $row = array_combine($header, $data);
                $rows[] = $row;
            }
            fclose($handle);
        } elseif (in_array($file->getClientOriginalExtension(), ['xlsx', 'xls'])) {
            $imported = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);
            $sheet = $imported[0] ?? [];
            if (count($sheet) < 2) {
                return response()->json(['success' => false, 'message' => 'Excel file is empty or missing header.'], 422);
            }
            $header = array_map('strtolower', $sheet[0]);
            for ($i = 1; $i < count($sheet); $i++) {
                $row = array_combine($header, $sheet[$i]);
                $rows[] = $row;
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unsupported file type.'], 422);
        }

        foreach ($rows as $i => $row) {
            $staffId = $row['staff id'] ?? null;
            $name = $row['full name'] ?? null;
            $email = $row['email'] ?? null;
            $department = $row['department'] ?? null;
            $title = $row['title'] ?? null;
            if (!$staffId || !$name || !$email) {
                $errors[] = "Row ".($i+2).": Missing required fields.";
                continue;
            }
            if (\App\Models\Lecturer::where('staff_id', $staffId)->exists()) {
                $errors[] = "Row ".($i+2).": Staff ID already exists ($staffId).";
                continue;
            }
            if (\App\Models\Lecturer::where('email', $email)->exists()) {
                $errors[] = "Row ".($i+2).": Email already exists ($email).";
                continue;
            }
            \App\Models\Lecturer::create([
                'staff_id' => $staffId,
                'name' => $name,
                'email' => $email,
                'department' => $department,
                'title' => $title,
                'password' => bcrypt('password123'),
                'is_active' => true,
            ]);
            $created++;
        }

        return response()->json([
            'success' => true,
            'created' => $created,
            'errors' => $errors,
        ]);
    }

    public function stats()
    {
        $total = \App\Models\Lecturer::count();
        $active = \App\Models\Lecturer::where('is_active', true)->count();
        $inactive = \App\Models\Lecturer::where('is_active', false)->count();
        $lastUpload = \App\Models\Lecturer::orderBy('created_at', 'desc')->value('created_at'); // returns null if none
        return response()->json([
            'success' => true,
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'last_upload' => $lastUpload,
        ]);
    }

    /**
     * Superadmin advanced lecturer details page
     */
    public function details($id)
    {
        $lecturer = Lecturer::with([
            'user',
            'department',
            'courses.academicLevel',
            'courses.departments',
        ])->findOrFail($id);

        // Aggregate simple metrics
        $classroomsCount = Classroom::where('lecturer_id', $id)->count();
        $activeClassrooms = Classroom::where('lecturer_id', $id)->where('is_active', true)->count();

        $recentClassrooms = Classroom::with(['course', 'course.academicLevel'])
            ->withCount('students')
            ->where('lecturer_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('superadmin.lecturer_details', compact('lecturer', 'classroomsCount', 'activeClassrooms', 'recentClassrooms'));
    }

    // Web-specific methods with flash notifications
    public function storeWeb(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|unique:lecturers',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'department' => 'nullable',
            'title' => 'nullable',
            'password' => 'required|min:6',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Create user first
        $user = \App\Models\User::create([
            'username' => strtolower(str_replace(['.', ' '], ['', ''], $request->staff_id)),
            'email' => $request->email,
            'full_name' => $request->name,
            'password' => bcrypt($request->password),
            'role' => 'lecturer',
            'is_active' => true,
        ]);
        
        // Get department ID if provided
        $departmentId = null;
        if ($request->department) {
            $department = \App\Models\Department::where('name', $request->department)->first();
            $departmentId = $department ? $department->id : null;
        }
        
        // Create lecturer
        Lecturer::create([
            'user_id' => $user->id,
            'staff_id' => $request->staff_id,
            'department_id' => $departmentId,
            'title' => $request->title,
            'is_active' => true,
        ]);
        
        return redirect()->route('superadmin.lecturers')->with('success', 'Lecturer added successfully!');
    }

    public function updateWeb(Request $request, $id)
    {
        $lecturer = Lecturer::with('user')->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|unique:lecturers,staff_id,' . $id,
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $lecturer->user_id,
            'department' => 'nullable',
            'title' => 'nullable',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Update user
        $lecturer->user->update([
            'full_name' => $request->name,
            'email' => $request->email,
        ]);
        
        // Get department ID if provided
        $departmentId = null;
        if ($request->department) {
            $department = \App\Models\Department::where('name', $request->department)->first();
            $departmentId = $department ? $department->id : null;
        }
        
        // Update lecturer
        $lecturer->update([
            'staff_id' => $request->staff_id,
            'department_id' => $departmentId,
            'title' => $request->title,
        ]);
        
        return redirect()->route('superadmin.lecturers')->with('success', 'Lecturer updated successfully!');
    }

    public function destroyWeb($id)
    {
        $lecturer = Lecturer::with('user')->findOrFail($id);
        
        // Delete the associated user
        if ($lecturer->user) {
            $lecturer->user->delete();
        }
        
        // Delete the lecturer
        $lecturer->delete();
        
        return redirect()->route('superadmin.lecturers')->with('success', 'Lecturer deleted successfully!');
    }

    public function bulkUploadWeb(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            if ($extension === 'csv') {
                $rows = array_map('str_getcsv', file($file->getPathname()));
                $header = array_shift($rows);
            } else {
                $import = new HeadingRowImport();
                $rows = \Maatwebsite\Excel\Facades\Excel::toArray($import, $file)[0];
                $header = array_keys($rows[0] ?? []);
                $rows = array_values($rows);
            }

            $errors = [];
            $created = 0;

            foreach ($rows as $i => $row) {
                if ($extension === 'csv') {
                    $rowData = array_combine($header, $row);
                } else {
                    $rowData = $row;
                }

                $staffId = $rowData['staff id'] ?? null;
                $name = $rowData['full name'] ?? null;
                $email = $rowData['email'] ?? null;
                $department = $rowData['department'] ?? null;
                $title = $rowData['title'] ?? null;

                if (!$staffId || !$name || !$email) {
                    $errors[] = "Row " . ($i + 2) . ": Missing required fields.";
                    continue;
                }

                if (Lecturer::where('staff_id', $staffId)->exists()) {
                    $errors[] = "Row " . ($i + 2) . ": Staff ID already exists ($staffId).";
                    continue;
                }

                if (\App\Models\User::where('email', $email)->exists()) {
                    $errors[] = "Row " . ($i + 2) . ": Email already exists ($email).";
                    continue;
                }

                // Create user first
                $user = \App\Models\User::create([
                    'username' => strtolower(str_replace(['.', ' '], ['', ''], $staffId)),
                    'email' => $email,
                    'full_name' => $name,
                    'password' => bcrypt('password123'),
                    'role' => 'lecturer',
                    'is_active' => true,
                ]);
                
                // Get department ID if provided
                $departmentId = null;
                if ($department) {
                    $dept = \App\Models\Department::where('name', $department)->first();
                    $departmentId = $dept ? $dept->id : null;
                }
                
                // Create lecturer
                Lecturer::create([
                    'user_id' => $user->id,
                    'staff_id' => $staffId,
                    'department_id' => $departmentId,
                    'title' => $title,
                    'is_active' => true,
                ]);
                $created++;
            }

            $message = "Upload completed. $created lecturers created.";
            if (count($errors) > 0) {
                $message .= " " . count($errors) . " errors occurred.";
            }

            return redirect()->route('superadmin.lecturers')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    /**
     * Update lecturer password
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string|min:6',
        ], [
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'Password must be at least 6 characters.',
            'new_password.confirmed' => 'Password confirmation does not match.',
            'new_password_confirmation.required' => 'Password confirmation is required.',
        ]);

        try {
            $lecturer = Lecturer::with('user')->findOrFail($id);
            
            if (!$lecturer->user) {
                return redirect()->route('superadmin.lecturers')->with('error', 'Lecturer user account not found.');
            }

            // Update the password
            $lecturer->user->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Log the password change
            \Log::info('Lecturer password updated by superadmin', [
                'lecturer_id' => $lecturer->id,
                'staff_id' => $lecturer->staff_id,
                'user_id' => $lecturer->user->id,
                'updated_by' => auth('superadmin')->id()
            ]);

            return redirect()->route('superadmin.lecturers')->with('success', 'Lecturer password updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to update lecturer password', [
                'lecturer_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('superadmin.lecturers')->with('error', 'Failed to update password: ' . $e->getMessage());
        }
    }
}