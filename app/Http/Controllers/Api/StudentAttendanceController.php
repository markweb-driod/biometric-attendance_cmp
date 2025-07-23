<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\FaceVerificationService;

class StudentAttendanceController extends Controller
{
    public function validateStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'matric' => 'required|string',
            'code' => 'required|string', // changed from pin
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::where('matric_number', $request->matric)
            ->where('is_active', true)
            ->first();

        $session = \App\Models\AttendanceSession::where('code', $request->code)
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Matric Number'
            ], 404);
        }

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Attendance Code'
            ], 404);
        }

        $classroom = $session->classroom;

        // Check if student is enrolled in this class
        $isEnrolled = $student->classrooms()->where('classroom_id', $classroom->id)->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Student not enrolled in this class'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'matric_number' => $student->matric_number,
                    'full_name' => $student->full_name,
                ],
                'classroom' => [
                    'id' => $classroom->id,
                    'class_name' => $classroom->class_name,
                    'course_code' => $classroom->course_code,
                ],
                'session' => [
                    'id' => $session->id,
                    'code' => $session->code,
                ]
            ]
        ]);
    }

    /**
     * Fetch student and class details for attendance capture page
     */
    public function fetchDetails(Request $request)
    {
        $request->validate([
            'matric_number' => 'required|string',
            'attendance_code' => 'required|string',
        ]);

        $student = \App\Models\Student::where('matric_number', $request->matric_number)->where('is_active', true)->first();
        $session = \App\Models\AttendanceSession::where('code', $request->attendance_code)->where('is_active', true)->first();
        if (!$session || !$session->is_active) {
            return response()->json(['success' => false, 'message' => 'Attendance session is not active.'], 403);
        }

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Invalid Matric Number'], 404);
        }
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Invalid Attendance Code'], 404);
        }
        $classroom = $session->classroom;
        // Check if student is enrolled in this class
        $isEnrolled = $student->classrooms()->where('classroom_id', $classroom->id)->exists();
        if (!$isEnrolled) {
            return response()->json(['success' => false, 'message' => 'Student not enrolled in this class'], 403);
        }
        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->full_name ?? $student->name ?? '',
                'matric_number' => $student->matric_number,
            ],
            'classroom' => [
                'id' => $classroom->id,
                'name' => $classroom->class_name ?? $classroom->name ?? '',
                'code' => $classroom->course_code,
            ],
            'session' => [
                'id' => $session->id,
                'code' => $session->code,
            ],
        ]);
    }

    /**
     * Capture attendance for student (simple version)
     */
    public function captureAttendance(Request $request)
    {
        $request->validate([
            'matric_number' => 'required|string',
            'attendance_code' => 'required|string',
            'image' => 'required|string',
        ]);
        $student = \App\Models\Student::where('matric_number', $request->matric_number)->where('is_active', true)->first();
        $session = \App\Models\AttendanceSession::where('code', $request->attendance_code)->where('is_active', true)->first();
        if (!$session || !$session->is_active) {
            return response()->json(['success' => false, 'message' => 'Attendance session is not active.'], 403);
        }
        if (!$student || !$session) {
            return response()->json(['success' => false, 'message' => 'Invalid details'], 404);
        }
        $classroom = $session->classroom;
        // Check if student is enrolled in this class
        $isEnrolled = $student->classrooms()->where('classroom_id', $classroom->id)->exists();
        if (!$isEnrolled) {
            return response()->json(['success' => false, 'message' => 'Student not enrolled in this class'], 403);
        }
        // Check if attendance already exists for this session
        $alreadyMarked = \App\Models\Attendance::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->exists();
        if ($alreadyMarked) {
            return response()->json(['success' => false, 'message' => 'Attendance already marked for this session.']);
        }
        // Facial verification before marking attendance
        if (!$student->reference_image_path) {
            return response()->json(['success' => false, 'message' => 'No reference image found for this student. Please register your face first.'], 400);
        }
        $faceResult = (new FaceVerificationService())->verifyFace($student->id, $request->image);
        if (!$faceResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Face verification failed. Please ensure your face is clearly visible and matches your registration photo.',
                'confidence' => $faceResult['confidence'] ?? 0,
                'raw' => $faceResult['raw'] ?? null,
            ], 401);
        }
        // Mark attendance
        $imageData = $request->input('image');
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $image = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif
            $image = base64_decode($image);
            if ($image === false) {
                return response()->json(['success' => false, 'message' => 'Invalid image data.'], 400);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid image format.'], 400);
        }
        $fileName = 'attendance/' . uniqid('att_') . '.' . $type;
        \Storage::disk('public')->put($fileName, $image);

        $attendance = new \App\Models\Attendance();
        $attendance->student_id = $student->id;
        $attendance->classroom_id = $classroom->id;
        $attendance->attendance_session_id = $session->id;
        $attendance->status = 'present';
        $attendance->image_path = $fileName;
        $attendance->captured_at = now();
        $attendance->save();
        return response()->json(['success' => true, 'message' => 'Attendance captured successfully.']);
    }

    // Add Haversine helper
    private function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2, $earthRadius = 6371000)
    {
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
