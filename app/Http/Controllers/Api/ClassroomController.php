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
        $classes = Classroom::where('lecturer_id', $lecturerId)
            ->with(['course.academicLevel', 'students'])
            ->get()
            ->map(function($class) {
                return [
                    'id' => $class->id,
                    'class_name' => $class->class_name,
                    'course_id' => $class->course_id,
                    'course_code' => $class->course->course_code ?? 'N/A',
                    'course_name' => $class->course->course_name ?? 'N/A',
                    'level' => $class->course->academicLevel->name ?? 'N/A',
                    'schedule' => $class->schedule,
                    'description' => $class->description,
                    'pin' => $class->pin,
                    'is_active' => $class->is_active,
                    'student_count' => $class->students->count(),
                    'created_at' => $class->created_at,
                    'updated_at' => $class->updated_at,
                ];
            });
        return response()->json(['success' => true, 'data' => $classes]);
    }

    // Create a new class
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'pin' => 'required|string|max:20|unique:classrooms',
            'schedule' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Verify lecturer is assigned to this course
        $lecturer = Lecturer::findOrFail($request->lecturer_id);
        if (!$lecturer->isAssignedToCourse($request->course_id)) {
            return response()->json([
                'success' => false, 
                'message' => 'Lecturer is not assigned to this course. Please assign the course to the lecturer first.'
            ], 403);
        }
        
        $class = Classroom::create([
            'class_name' => $request->class_name,
            'course_id' => $request->course_id,
            'lecturer_id' => $request->lecturer_id,
            'pin' => strtoupper($request->pin),
            'schedule' => $request->schedule,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);
        // Load the course to get departments and academic level info
        $class->load(['course.departments', 'course.academicLevel']);
        
        // Attach students of the correct level and any associated departments
        if ($class->course && $class->course->academicLevel) {
            $departmentIds = $class->course->departments ? $class->course->departments->pluck('id')->all() : [];
            if (!empty($departmentIds)) {
                $students = \App\Models\Student::whereIn('department_id', $departmentIds)
                    ->where('academic_level_id', $class->course->academicLevel->id)
                    ->where('is_active', true)
                    ->pluck('id');
                $class->students()->sync($students);
            }
        }
        return response()->json(['success' => true, 'data' => $class]);
    }

    // Show a single class
    public function show($id)
    {
        $class = Classroom::with(['course.academicLevel', 'students'])->find($id);
        if (!$class) {
            return response()->json(['success' => false, 'message' => 'Class not found'], 404);
        }
        
        $classData = [
            'id' => $class->id,
            'class_name' => $class->class_name,
            'course_id' => $class->course_id,
            'course_code' => $class->course->course_code ?? 'N/A',
            'course_name' => $class->course->course_name ?? 'N/A',
            'level' => $class->course->academicLevel->name ?? 'N/A',
            'schedule' => $class->schedule,
            'description' => $class->description,
            'pin' => $class->pin,
            'is_active' => $class->is_active,
            'student_count' => $class->students->count(),
            'created_at' => $class->created_at,
            'updated_at' => $class->updated_at,
        ];
        
        return response()->json(['success' => true, 'data' => $classData]);
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
            'course_id' => 'sometimes|required|exists:courses,id',
            'pin' => 'sometimes|required|string|unique:classrooms,pin,' . $id,
            'schedule' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // If course_id is being updated, verify lecturer is assigned to the new course
        if ($request->has('course_id') && $request->course_id != $class->course_id) {
            $lecturer = Lecturer::findOrFail($class->lecturer_id);
            if (!$lecturer->isAssignedToCourse($request->course_id)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Lecturer is not assigned to this course. Please assign the course to the lecturer first.'
                ], 403);
            }
        }
        
        $class->update($request->only(['class_name', 'course_id', 'pin', 'schedule', 'description', 'is_active']));
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