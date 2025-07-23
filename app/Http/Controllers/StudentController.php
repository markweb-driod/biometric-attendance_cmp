<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function landing()
    {
        return view('student_landing');
    }

    public function validateStudent(Request $request)
    {
        $request->validate([
            'matric_number' => 'required|string',
            'class_code' => 'required|string',
        ]);

        $student = \App\Models\Student::where('matric_number', $request->matric_number)->first();
        $classroom = \App\Models\Classroom::where('code', $request->class_code)->first();

        if ($student && $classroom) {
            // Check if student is in the class (assuming many-to-many)
            if ($student->classrooms()->where('classroom_id', $classroom->id)->exists()) {
                return response()->json([
                    'success' => true,
                    'student' => [
                        'name' => $student->name,
                        'matric_number' => $student->matric_number,
                        'class_name' => $classroom->name,
                        'class_code' => $classroom->code,
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not registered in this class.'
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Invalid matric number or class code.'
        ]);
    }

    public function validateMatricForFaceRegistration(Request $request)
    {
        $request->validate(['matric_number' => 'required|string']);
        $student = \App\Models\Student::where('matric_number', $request->matric_number)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Matric number not found.']);
        }
        if (!$student->face_registration_enabled) {
            return response()->json(['success' => false, 'message' => 'Face registration is not enabled for this student.', 'face_registration_enabled' => false]);
        }
        return response()->json(['success' => true, 'face_registration_enabled' => true]);
    }

    public function registerFace(Request $request)
    {
        $request->validate([
            'reference_image' => 'required|string',
        ]);
        // Identify student (auth or by matric number)
        $student = auth()->user();
        if (!$student || !$student instanceof \App\Models\Student) {
            $matric = $request->input('matric_number');
            $student = \App\Models\Student::where('matric_number', $matric)->firstOrFail();
        }
        $imageData = $request->input('reference_image');
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $image = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]);
            $image = base64_decode($image);
            if ($image === false) {
                return back()->withErrors(['Invalid image data.']);
            }
        } else {
            return back()->withErrors(['Invalid image format.']);
        }
        $fileName = 'reference_images/' . $student->matric_number . '_' . uniqid() . '.' . $type;
        \Storage::disk('public')->put($fileName, $image);
        $student->reference_image_path = $fileName;
        $student->save();
        return redirect()->back()->with('success', 'Face registered successfully!');
    }
}
