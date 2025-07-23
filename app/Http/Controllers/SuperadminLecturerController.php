<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Lecturer;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\Log;

class SuperadminLecturerController extends Controller
{
    public function index(Request $request)
    {
        $query = Lecturer::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('staff_id', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('department', 'like', "%$search%") ;
            });
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        $lecturers = $query->orderBy('name')->get();
        return response()->json(['success' => true, 'data' => $lecturers]);
    }

    public function show($id)
    {
        $lecturer = Lecturer::findOrFail($id);
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
}

class SuperadminClassController extends Controller
{
    public function index(Request $request)
    {
        $query = Classroom::with('lecturer');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('class_name', 'like', "%$search%")
                  ->orWhere('course_code', 'like', "%$search%")
                  ->orWhere('pin', 'like', "%$search%")
                  ->orWhere('schedule', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%") ;
            });
        }
        if ($request->filled('level')) {
            $query->where('academic_level', $request->level);
        }
        if ($request->filled('lecturer')) {
            $query->where('lecturer_id', $request->lecturer);
        }
        $classes = $query->orderBy('class_name')->get()->map(function($c) {
            return [
                'id' => $c->id,
                'class_name' => $c->class_name,
                'course_code' => $c->course_code,
                'academic_level' => $c->academic_level ?? '',
                'lecturer_id' => $c->lecturer_id,
                'lecturer_name' => $c->lecturer ? $c->lecturer->name : '',
                'schedule' => $c->schedule,
                'description' => $c->description,
                'pin' => $c->pin,
                'is_active' => $c->is_active,
            ];
        });
        return response()->json(['success' => true, 'data' => $classes]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required',
            'course_code' => 'required',
            'academic_level' => 'required',
            'lecturer_id' => 'required|exists:lecturers,id',
            'schedule' => 'nullable',
            'description' => 'nullable',
            'pin' => 'required|unique:classrooms,pin',
        ]);
        $class = \App\Models\Classroom::create([
            'class_name' => $request->class_name,
            'course_code' => $request->course_code,
            'academic_level' => $request->academic_level,
            'lecturer_id' => $request->lecturer_id,
            'schedule' => $request->schedule,
            'description' => $request->description,
            'pin' => $request->pin,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius ?? 50,
            'is_active' => true,
        ]);
        return response()->json(['success' => true, 'data' => $class]);
    }
    public function show($id)
    {
        $class = \App\Models\Classroom::with('lecturer')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $class]);
    }
    public function update(Request $request, $id)
    {
        $class = \App\Models\Classroom::findOrFail($id);
        $request->validate([
            'class_name' => 'required',
            'course_code' => 'required',
            'academic_level' => 'required',
            'lecturer_id' => 'required|exists:lecturers,id',
            'schedule' => 'nullable',
            'description' => 'nullable',
            'pin' => 'required|unique:classrooms,pin,' . $id,
        ]);
        $class->update([
            'class_name' => $request->class_name,
            'course_code' => $request->course_code,
            'academic_level' => $request->academic_level,
            'lecturer_id' => $request->lecturer_id,
            'schedule' => $request->schedule,
            'description' => $request->description,
            'pin' => $request->pin,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius ?? 50,
        ]);
        return response()->json(['success' => true, 'data' => $class]);
    }
    public function destroy($id)
    {
        $class = \App\Models\Classroom::findOrFail($id);
        $class->delete();
        return response()->json(['success' => true]);
    }
}

class SuperadminAttendanceController extends Controller
{
    public function index(Request $request)
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
                'status' => 'Present',
                'method' => $a->image_path ? 'Biometric' : 'Manual',
            ];
        });
        return response()->json(['success' => true, 'data' => $records]);
    }
} 