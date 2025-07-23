<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\Log;

class SuperadminStudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('matric_number', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('academic_level', 'like', "%$search%") ;
            });
        }
        if ($request->filled('level')) {
            $query->where('academic_level', $request->level);
        }
        $students = $query->orderBy('full_name')->paginate(20);
        return response()->json(['success' => true, 'data' => $students]);
    }

    public function show($id)
    {
        $student = Student::findOrFail($id);
        return response()->json(['success' => true, 'data' => $student]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'matric_number' => 'required|unique:students',
            'full_name' => 'required',
            'email' => 'nullable|email|unique:students',
            'academic_level' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $student = Student::create($request->only(['matric_number', 'full_name', 'email', 'academic_level']));
        return response()->json(['success' => true, 'data' => $student]);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'matric_number' => 'required|unique:students,matric_number,' . $id,
            'full_name' => 'required',
            'email' => 'nullable|email|unique:students,email,' . $id,
            'academic_level' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $student->update($request->only(['matric_number', 'full_name', 'email', 'academic_level']));
        return response()->json(['success' => true, 'data' => $student]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return response()->json(['success' => true]);
    }

    // Bulk upload (CSV)
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
            $fullName = $row['full name'] ?? null;
            $matric = $row['matric number'] ?? null;
            $level = $row['level'] ?? null;
            if (!$fullName || !$matric || !$level) {
                $errors[] = "Row ".($i+2).": Missing required fields.";
                continue;
            }
            if (\App\Models\Student::where('matric_number', $matric)->exists()) {
                $errors[] = "Row ".($i+2).": Matric number already exists ($matric).";
                continue;
            }
            \App\Models\Student::create([
                'full_name' => $fullName,
                'matric_number' => $matric,
                'academic_level' => $level,
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
        $total = \App\Models\Student::count();
        $active = \App\Models\Student::where('is_active', true)->count();
        $inactive = \App\Models\Student::where('is_active', false)->count();
        $lastUpload = \App\Models\Student::orderBy('created_at', 'desc')->value('created_at');
        return response()->json([
            'success' => true,
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'last_upload' => $lastUpload,
        ]);
    }

    public function enableFaceRegistration($id)
    {
        $student = \App\Models\Student::findOrFail($id);
        $student->face_registration_enabled = true;
        $student->save();
        return redirect()->back()->with('success', 'Face registration enabled for student.');
    }

    public function disableFaceRegistration($id)
    {
        $student = \App\Models\Student::findOrFail($id);
        $student->face_registration_enabled = false;
        $student->save();
        return redirect()->back()->with('success', 'Face registration disabled for student.');
    }

    public function enableFaceRegistrationAll()
    {
        \App\Models\Student::query()->update(['face_registration_enabled' => true]);
        return response()->json(['success' => true, 'message' => 'Face registration enabled for all students.']);
    }

    public function disableFaceRegistrationAll()
    {
        \App\Models\Student::query()->update(['face_registration_enabled' => false]);
        return response()->json(['success' => true, 'message' => 'Face registration disabled for all students.']);
    }

    public function faceRegistrationStatus()
    {
        $total = \App\Models\Student::count();
        $enabled = \App\Models\Student::where('face_registration_enabled', true)->count();
        $disabled = \App\Models\Student::where('face_registration_enabled', false)->count();
        $status = 'partial';
        if ($total > 0 && $enabled === $total) {
            $status = 'all_enabled';
        } elseif ($total > 0 && $disabled === $total) {
            $status = 'all_disabled';
        }
        return response()->json(['status' => $status, 'total' => $total, 'enabled' => $enabled, 'disabled' => $disabled]);
    }

    // Student Face Registration Management Page
    public function faceRegistrationManagement() {
        return view('superadmin.face_registration_management');
    }
    // Data API for table (with filters)
    public function faceRegistrationData(Request $request) {
        $query = \App\Models\Student::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('matric_number', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('academic_level', 'like', "%$search%") ;
            });
        }
        if ($request->filled('level')) {
            $query->where('academic_level', $request->level);
        }
        if ($request->filled('face_status')) {
            if ($request->face_status === 'registered') {
                $query->whereNotNull('reference_image_path');
            } elseif ($request->face_status === 'not_registered') {
                $query->whereNull('reference_image_path');
            }
        }
        $students = $query->orderBy('full_name')->paginate($request->get('per_page', 20));
        return response()->json(['success' => true, 'data' => $students]);
    }
    // Update face image (re-register)
    public function updateFaceImage(Request $request, $id) {
        $student = \App\Models\Student::findOrFail($id);
        $request->validate(['reference_image' => 'required|string']);
        $imageData = $request->input('reference_image');
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $image = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]);
            $image = base64_decode($image);
            if ($image === false) {
                return response()->json(['success' => false, 'message' => 'Invalid image data.']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid image format.']);
        }
        $fileName = 'reference_images/' . $student->matric_number . '_' . uniqid() . '.' . $type;
        \Storage::disk('public')->put($fileName, $image);
        $student->reference_image_path = $fileName;
        $student->save();
        return response()->json(['success' => true, 'message' => 'Face image updated successfully!', 'image' => $fileName]);
    }
    // Delete face image
    public function deleteFaceImage($id) {
        $student = \App\Models\Student::findOrFail($id);
        if ($student->reference_image_path) {
            \Storage::disk('public')->delete($student->reference_image_path);
            $student->reference_image_path = null;
            $student->save();
        }
        return response()->json(['success' => true, 'message' => 'Face image deleted.']);
    }
    // Enable/disable face registration (reuse existing methods)
    // Bulk actions
    public function bulkFaceRegistrationAction(Request $request) {
        $request->validate(['action' => 'required|string', 'ids' => 'required|array']);
        $ids = $request->ids;
        $action = $request->action;
        $count = 0;
        foreach ($ids as $id) {
            $student = \App\Models\Student::find($id);
            if (!$student) continue;
            if ($action === 'enable') {
                $student->face_registration_enabled = true;
                $student->save();
                $count++;
            } elseif ($action === 'disable') {
                $student->face_registration_enabled = false;
                $student->save();
                $count++;
            } elseif ($action === 'delete_image') {
                if ($student->reference_image_path) {
                    \Storage::disk('public')->delete($student->reference_image_path);
                    $student->reference_image_path = null;
                    $student->save();
                    $count++;
                }
            }
        }
        return response()->json(['success' => true, 'message' => 'Bulk action completed.', 'count' => $count]);
    }
    // Export to CSV
    public function exportFaceRegistration(Request $request) {
        $query = \App\Models\Student::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('matric_number', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('academic_level', 'like', "%$search%") ;
            });
        }
        if ($request->filled('level')) {
            $query->where('academic_level', $request->level);
        }
        if ($request->filled('face_status')) {
            if ($request->face_status === 'registered') {
                $query->whereNotNull('reference_image_path');
            } elseif ($request->face_status === 'not_registered') {
                $query->whereNull('reference_image_path');
            }
        }
        $students = $query->orderBy('full_name')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="face_registration.csv"',
        ];
        $callback = function() use ($students) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Full Name', 'Matric Number', 'Email', 'Level', 'Face Registered', 'Face Registration Enabled']);
            foreach ($students as $s) {
                fputcsv($handle, [
                    $s->full_name,
                    $s->matric_number,
                    $s->email,
                    $s->academic_level,
                    $s->reference_image_path ? 'Yes' : 'No',
                    $s->face_registration_enabled ? 'Yes' : 'No',
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }
} 