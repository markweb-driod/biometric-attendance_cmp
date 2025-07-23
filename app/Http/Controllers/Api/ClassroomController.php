<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassroomController extends Controller
{
    // List all classes for a lecturer
    public function index(Request $request)
    {
        $lecturerId = $request->input('lecturer_id');
        if (!$lecturerId) {
            return response()->json(['success' => false, 'message' => 'Lecturer ID required'], 400);
        }
        $classes = Classroom::where('lecturer_id', $lecturerId)->get();
        return response()->json(['success' => true, 'data' => $classes]);
    }

    // Create a new class
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_name' => 'required|string',
            'course_code' => 'required|string',
            'pin' => 'required|string|unique:classrooms,pin',
            'schedule' => 'nullable|string',
            'description' => 'nullable|string',
            'lecturer_id' => 'required|exists:lecturers,id',
            'level' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $class = Classroom::create([
            'class_name' => $request->class_name,
            'course_code' => $request->course_code,
            'pin' => $request->pin,
            'schedule' => $request->schedule,
            'description' => $request->description,
            'lecturer_id' => $request->lecturer_id,
            'is_active' => true,
            'level' => $request->level,
        ]);
        // Attach students of the correct level and department
        $students = \App\Models\Student::where('academic_level', $request->level)
            ->where('department', 'Computer Science')
            ->pluck('id');
        $class->students()->sync($students);
        return response()->json(['success' => true, 'data' => $class]);
    }

    // Update a class
    public function update(Request $request, $id)
    {
        $class = Classroom::find($id);
        if (!$class) {
            return response()->json(['success' => false, 'message' => 'Class not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'class_name' => 'sometimes|required|string',
            'course_code' => 'sometimes|required|string',
            'pin' => 'sometimes|required|string|unique:classrooms,pin,' . $id,
            'schedule' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'level' => 'sometimes|required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $class->update($request->only(['class_name', 'course_code', 'pin', 'schedule', 'description', 'is_active', 'level']));
        return response()->json(['success' => true, 'data' => $class]);
    }

    // Delete a class
    public function destroy($id)
    {
        $class = Classroom::find($id);
        if (!$class) {
            return response()->json(['success' => false, 'message' => 'Class not found'], 404);
        }
        $class->delete();
        return response()->json(['success' => true, 'message' => 'Class deleted']);
    }
} 