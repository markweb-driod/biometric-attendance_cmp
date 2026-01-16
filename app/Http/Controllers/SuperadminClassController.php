<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Lecturer;
use Illuminate\Http\Request;

class SuperadminClassController extends Controller
{
    public function apiIndex(Request $request)
    {
        $query = Classroom::with(['lecturer.user', 'course.academicLevel']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('class_name', 'like', "%$search%")
                  ->orWhere('pin', 'like', "%$search%")
                  ->orWhere('schedule', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('course', function($cq) use ($search) {
                      $cq->where('course_code', 'like', "%$search%")
                         ->orWhere('course_name', 'like', "%$search%");
                  })
                  ->orWhereHas('lecturer.user', function($lq) use ($search) {
                      $lq->where('full_name', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%");
                  });
            });
        }
        if ($request->filled('level')) {
            $query->whereHas('course.academicLevel', function($q) use ($request) {
                $q->where('name', $request->level);
            });
        }
        if ($request->filled('lecturer')) {
            $query->where('lecturer_id', $request->lecturer);
        }
        $classes = $query->orderBy('class_name')->get()->map(function($c) {
            return [
                'id' => $c->id,
                'class_name' => $c->class_name,
                'course_code' => $c->course ? $c->course->course_code : 'N/A',
                'academic_level' => $c->course && $c->course->academicLevel ? $c->course->academicLevel->name : 'N/A',
                'lecturer_id' => $c->lecturer_id,
                'lecturer_name' => $c->lecturer && $c->lecturer->user ? $c->lecturer->user->full_name : 'Unassigned',
                'schedule' => $c->schedule,
                'description' => $c->description,
                'pin' => $c->pin,
                'is_active' => $c->is_active,
            ];
        });
        return response()->json(['success' => true, 'data' => $classes]);
    }

    public function index(Request $request)
    {
        $query = \App\Models\Classroom::with(['lecturer.user', 'course.academicLevel']);
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('class_name', 'like', "%$search%")
                  ->orWhere('course_code', 'like', "%$search%")
                  ->orWhereHas('lecturer', function($lq) use ($search) {
                      $lq->where('name', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%")
                         ->orWhere('staff_id', 'like', "%$search%")
                         ->orWhere('department', 'like', "%$search%")
                         ->orWhere('title', 'like', "%$search%")
                         ;
                  });
            });
        }
        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }
        if ($request->filled('lecturer')) {
            $query->where('lecturer_id', $request->input('lecturer'));
        }
        $classes = $query->paginate(20);
        $levels = \App\Models\Classroom::select('level')->distinct()->pluck('level');
        $lecturers = \App\Models\Lecturer::all();
        $stats = $this->getStats();
        return view('superadmin.classes', compact('classes', 'levels', 'lecturers', 'stats'));
    }

    public function show($id)
    {
        $class = Classroom::with(['lecturer.user', 'course.academicLevel', 'students.user'])->findOrFail($id);
        
        $classData = [
            'id' => $class->id,
            'class_name' => $class->class_name,
            'course_id' => $class->course_id,
            'course_code' => $class->course ? $class->course->course_code : 'N/A',
            'academic_level' => $class->course && $class->course->academicLevel ? $class->course->academicLevel->name : 'N/A',
            'lecturer_id' => $class->lecturer_id,
            'lecturer_name' => $class->lecturer && $class->lecturer->user ? $class->lecturer->user->full_name : 'Unassigned',
            'schedule' => $class->schedule,
            'description' => $class->description,
            'pin' => $class->pin,
            'is_active' => $class->is_active,
            'created_at' => $class->created_at,
            'updated_at' => $class->updated_at,
        ];
        
        return response()->json(['success' => true, 'data' => $classData]);
    }

    public function stats()
    {
        return response()->json($this->getStats());
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'schedule' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'pin' => 'nullable|string|max:20|unique:classrooms,pin',
        ]);

        $class = Classroom::create([
            'class_name' => $request->class_name,
            'course_id' => $request->course_id,
            'lecturer_id' => $request->lecturer_id,
            'schedule' => $request->schedule,
            'description' => $request->description,
            'pin' => $request->pin ?? Classroom::generatePin(),
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Class created successfully', 'data' => $class]);
    }

    public function update(Request $request, $id)
    {
        $class = \App\Models\Classroom::findOrFail($id);
        $request->validate([
            'class_name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'schedule' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'pin' => 'nullable|string|max:20|unique:classrooms,pin,' . $id,
        ]);

        $class->update([
            'class_name' => $request->class_name,
            'course_id' => $request->course_id,
            'lecturer_id' => $request->lecturer_id,
            'schedule' => $request->schedule,
            'description' => $request->description,
            'pin' => $request->pin ?? $class->pin,
        ]);

        return response()->json(['success' => true, 'message' => 'Class updated successfully', 'data' => $class]);
    }

    public function destroy($id)
    {
        $class = \App\Models\Classroom::findOrFail($id);
        $class->delete();
        return response()->json(['success' => true]);
    }

    private function getStats()
    {
        return [
            'total' => \App\Models\Classroom::count(),
            'active' => \App\Models\Classroom::where('is_active', true)->count(),
            'inactive' => \App\Models\Classroom::where('is_active', false)->count(),
            'last_created' => \App\Models\Classroom::orderByDesc('created_at')->value('created_at'),
        ];
    }
}