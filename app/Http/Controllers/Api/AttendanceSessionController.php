<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\Lecturer;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AttendanceSessionController extends Controller
{
    // Create a new attendance session
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'classroom_id' => 'required|exists:classrooms,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $code = strtoupper(Str::random(6));
        $session = AttendanceSession::create([
            'classroom_id' => $request->classroom_id,
            'lecturer_id' => $request->lecturer_id,
            'code' => $code,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => true,
        ]);
        return response()->json(['success' => true, 'data' => $session]);
    }

    // Update/close a session or regenerate code
    public function update(Request $request, $id)
    {
        $session = AttendanceSession::find($id);
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Session not found'], 404);
        }
        if ($request->has('close') && $request->close) {
            $session->is_active = false;
            $session->end_time = Carbon::now();
        }
        if ($request->has('regenerate_code') && $request->regenerate_code) {
            $session->code = strtoupper(Str::random(6));
        }
        if ($request->has('end_time')) {
            $session->end_time = $request->end_time;
        }
        $session->save();
        return response()->json(['success' => true, 'data' => $session]);
    }

    // List active/past sessions for a lecturer
    public function index(Request $request)
    {
        $lecturerId = $request->input('lecturer_id');
        if (!$lecturerId) {
            return response()->json(['success' => false, 'message' => 'Lecturer ID required'], 400);
        }
        $sessions = AttendanceSession::where('lecturer_id', $lecturerId)
            ->with('classroom')
            ->orderBy('is_active', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();
        return response()->json(['success' => true, 'data' => $sessions]);
    }

    // List students who have marked attendance in a session
    public function students($id)
    {
        $session = AttendanceSession::find($id);
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Session not found'], 404);
        }
        $attendances = Attendance::where('classroom_id', $session->classroom_id)
            ->whereBetween('captured_at', [$session->start_time, $session->end_time ?? now()])
            ->with('student')
            ->get();
        $students = $attendances->map(function($a) {
            return [
                'student_id' => $a->student_id,
                'matric_number' => $a->student->matric_number ?? '',
                'full_name' => $a->student->full_name ?? '',
                'captured_at' => $a->captured_at,
                'image_path' => $a->image_path,
            ];
        });
        return response()->json(['success' => true, 'data' => $students]);
    }
} 