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
        $student = \App\Models\Student::with('user', 'academicLevel', 'department')->where('matric_number', $request->matric_number)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Matric number not found.']);
        }
        
        // Check if student has already registered their face
        if ($student->reference_image_path) {
            return response()->json([
                'success' => false, 
                'message' => 'Face already registered. Each student can only register their face once for security purposes.',
                'already_registered' => true,
                'student' => [
                    'full_name' => $student->user->full_name ?? '',
                    'matric_number' => $student->matric_number,
                    'academic_level' => $student->academicLevel->name ?? '',
                    'department' => $student->department->name ?? '',
                ]
            ]);
        }
       
        return response()->json([
            'success' => true,
            'face_registration_enabled' => true,
            'student' => [
                'full_name' => $student->user->full_name ?? '',
                'matric_number' => $student->matric_number,
                'academic_level' => $student->academicLevel->name ?? '',
                'department' => $student->department->name ?? '',
            ]
        ]);
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
        
        // Security check: Prevent duplicate face registration
        if ($student->reference_image_path) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Face already registered. Each student can only register their face once for security purposes.'
                ], 400);
            }
            return back()->withErrors(['Face already registered. Each student can only register their face once for security purposes.']);
        }
        
        $imageData = $request->input('reference_image');
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $image = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]);
            $image = base64_decode($image);
            if ($image === false) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Invalid image data.'], 400);
                }
                return back()->withErrors(['Invalid image data.']);
            }
        } else {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Invalid image format.'], 400);
            }
            return back()->withErrors(['Invalid image format.']);
        }
        
        $fileName = 'reference_images/' . $student->matric_number . '_' . uniqid() . '.' . $type;
        \Storage::disk('public')->put($fileName, $image);
        $student->reference_image_path = $fileName;
        $student->save();
        
        // Send email notification
        try {
            $student->load('user');
            if ($student->user && $student->user->email) {
                \Mail::to($student->user->email)->send(new \App\Mail\FaceRegistrationSuccessMail($student));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send face registration email', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            // Don't fail the registration if email fails
        }
        
        // Send SMS notification
        try {
            if ($student->phone) {
                $smsService = new \App\Services\SmsService();
                $studentName = $student->user ? $student->user->full_name : 'Student';
                $smsMessage = "Hello {$studentName}, your face registration for NSUK Biometric Attendance System was successful. Matric: {$student->matric_number}. You can now mark attendance using face recognition.";
                $smsService->sendSms($student->phone, $smsMessage);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send face registration SMS', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            // Don't fail the registration if SMS fails
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Face registered successfully! Email and SMS notifications have been sent to you.'
            ]);
        }
        
        return redirect()->back()->with('success', 'Face registered successfully! Email and SMS notifications have been sent to you.');
    }
}
