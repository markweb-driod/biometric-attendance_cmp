<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Lecturer;
use Illuminate\Http\Request;

class SuperadminClassController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Classroom::with('lecturer');
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

    private function getStats()
    {
        return [
            'total' => \App\Models\Classroom::count(),
            'active' => \App\Models\Classroom::where('status', 'active')->count(),
            'inactive' => \App\Models\Classroom::where('status', 'inactive')->count(),
            'last_created' => \App\Models\Classroom::orderByDesc('created_at')->value('created_at'),
        ];
    }

    public function stats()
    {
        return response()->json($this->getStats());
    }
} 