<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LecturerController extends Controller
{
    public function validateLecturer(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $lecturer = Lecturer::with(['user', 'department'])->where('staff_id', $request->staff_id)
            ->where('is_active', true)
            ->first();

        if (!$lecturer || !$lecturer->user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid staff ID or lecturer not found.',
            ], 401);
        }

        if (!Hash::check($request->password, $lecturer->user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'lecturer' => [
                    'id' => $lecturer->id,
                    'staff_id' => $lecturer->staff_id,
                    'name' => $lecturer->user->full_name,
                    'email' => $lecturer->user->email,
                    'department' => $lecturer->department->name ?? 'N/A',
                    'title' => $lecturer->title,
                ],
            ],
        ]);
    }

    public function getDashboard(Request $request)
    {
        // This would typically get the lecturer from session/auth
        // For now, we'll return sample data
        $lecturer = Lecturer::first(); // In real app, get from auth
        
        if (!$lecturer) {
            return response()->json([
                'success' => false,
                'message' => 'Lecturer not found.',
            ], 404);
        }

        $classes = $lecturer->classrooms()->with('students')->get();
        $totalStudents = $classes->sum(function($class) {
            return $class->students->count();
        });

        $recentAttendances = $lecturer->attendances()
            ->with(['student', 'classroom'])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'lecturer' => [
                    'id' => $lecturer->id,
                    'name' => $lecturer->name,
                    'department' => $lecturer->department,
                    'title' => $lecturer->title,
                ],
                'stats' => [
                    'total_classes' => $classes->count(),
                    'total_students' => $totalStudents,
                    'today_attendance' => 89, // Sample data
                    'recent_activity' => $recentAttendances->count(),
                ],
                'classes' => $classes,
                'recent_attendances' => $recentAttendances,
            ],
        ]);
    }

    public function attendance(Request $request)
    {
        $classIds = $request->input('class_ids', []);
        $date = $request->input('date');
        $lecturerId = $request->input('lecturer_id');
        if (empty($classIds) && $lecturerId) {
            $lecturer = \App\Models\Lecturer::find($lecturerId);
            if ($lecturer) {
                $classIds = $lecturer->classrooms()->pluck('id')->toArray();
            }
        }
        $query = \App\Models\Attendance::with(['student', 'classroom']);
        if (!empty($classIds)) {
            $query->whereIn('classroom_id', $classIds);
        }
        if ($date) {
            $query->whereDate('captured_at', $date);
        }
        $records = $query->orderByDesc('captured_at')->get();
        $data = $records->map(function($a) {
            return [
                'student_name' => $a->student->full_name ?? '',
                'matric_number' => $a->student->matric_number ?? '',
                'class_code' => $a->classroom->course_code ?? '',
                'class_name' => $a->classroom->class_name ?? '',
                'class_id' => $a->classroom_id,
                'captured_at' => $a->captured_at ? $a->captured_at->format('Y-m-d H:i:s') : '',
                'status' => $a->status ?? '',
            ];
        });
        return response()->json(['success' => true, 'data' => $data]);
    }
} 