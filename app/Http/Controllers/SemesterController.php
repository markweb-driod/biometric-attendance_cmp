<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SemesterController extends Controller
{
    /**
     * Display a listing of semesters
     */
    public function index(Request $request)
    {
        try {
            $query = Semester::query();

            // Filter by academic year if provided
            if ($request->has('academic_year') && $request->academic_year) {
                $query->where('academic_year', $request->academic_year);
            }

            // Filter by active status if provided
            if ($request->has('is_active') && $request->is_active !== '') {
                $query->where('is_active', $request->is_active);
            }

            $semesters = $query->orderBy('academic_year', 'desc')
                             ->orderBy('start_date', 'desc')
                             ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $semesters
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch semesters', [
                'error' => $e->getMessage(),
                'user_id' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch semesters: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all active semesters
     */
    public function getActive()
    {
        try {
            $semesters = Semester::getActive();

            return response()->json([
                'success' => true,
                'data' => $semesters
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch active semesters', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch active semesters: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current semester
     */
    public function getCurrent()
    {
        try {
            $semester = Semester::getCurrent();

            return response()->json([
                'success' => true,
                'data' => $semester
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch current semester', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch current semester: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created semester
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:10|unique:semesters',
            'academic_year' => 'required|string|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_current' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $semester = Semester::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'academic_year' => $request->academic_year,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'is_current' => $request->is_current ?? false,
            ]);

            // If this is set as current, deactivate others
            if ($request->is_current) {
                $semester->setAsCurrent();
            }

            Cache::forget('semesters_list');
            Cache::forget('current_semester');

            Log::info('Semester created', [
                'semester_id' => $semester->id,
                'semester_code' => $semester->code,
                'created_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Semester created successfully',
                'data' => $semester
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create semester', [
                'error' => $e->getMessage(),
                'semester_code' => $request->code
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create semester: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified semester
     */
    public function update(Request $request, $id)
    {
        $semester = Semester::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:10|unique:semesters,code,' . $id,
            'academic_year' => 'required|string|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_current' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $semester->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'academic_year' => $request->academic_year,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
                'is_active' => $request->is_active ?? $semester->is_active,
                'is_current' => $request->is_current ?? $semester->is_current,
            ]);

            // If this is set as current, deactivate others
            if ($request->is_current && !$semester->is_current) {
                $semester->setAsCurrent();
            }

            Cache::forget('semesters_list');
            Cache::forget('current_semester');

            Log::info('Semester updated', [
                'semester_id' => $semester->id,
                'semester_code' => $semester->code,
                'updated_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Semester updated successfully',
                'data' => $semester
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update semester', [
                'error' => $e->getMessage(),
                'semester_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update semester: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set semester as current
     */
    public function setCurrent(Request $request, $id)
    {
        try {
            $semester = Semester::findOrFail($id);
            $semester->setAsCurrent();

            Cache::forget('current_semester');

            Log::info('Semester set as current', [
                'semester_id' => $semester->id,
                'semester_code' => $semester->code,
                'updated_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Semester set as current successfully',
                'data' => $semester
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to set semester as current', [
                'error' => $e->getMessage(),
                'semester_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to set semester as current: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified semester
     */
    public function destroy($id)
    {
        try {
            $semester = Semester::findOrFail($id);

            // Check if semester has any related data
            $hasCourses = $semester->courses()->exists();
            $hasClassrooms = $semester->classrooms()->exists();
            $hasAttendances = $semester->attendances()->exists();

            if ($hasCourses || $hasClassrooms || $hasAttendances) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete semester with existing courses, classrooms, or attendance records'
                ], 422);
            }

            $semester->delete();

            Cache::forget('semesters_list');
            Cache::forget('current_semester');

            Log::info('Semester deleted', [
                'semester_id' => $id,
                'semester_code' => $semester->code,
                'deleted_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Semester deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete semester', [
                'error' => $e->getMessage(),
                'semester_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete semester: ' . $e->getMessage()
            ], 500);
        }
    }
}