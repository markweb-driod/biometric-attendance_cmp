<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SuperadminUserToggleController extends Controller
{
    /**
     * Toggle student active status
     */
    public function toggleStudent(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);
            $previousStatus = $student->is_active;
            
            $student->is_active = !$student->is_active;
            $student->save();
            
            Log::info('Student status toggled', [
                'student_id' => $id,
                'matric_number' => $student->matric_number,
                'previous_status' => $previousStatus,
                'new_status' => $student->is_active,
                'toggled_by' => auth('superadmin')->user()->id ?? 'system'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $student->is_active 
                    ? 'Student enabled successfully' 
                    : 'Student disabled successfully',
                'is_active' => $student->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to toggle student status', [
                'student_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle student status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle lecturer active status
     */
    public function toggleLecturer(Request $request, $id)
    {
        try {
            $lecturer = Lecturer::findOrFail($id);
            $previousStatus = $lecturer->is_active;
            
            $lecturer->is_active = !$lecturer->is_active;
            $lecturer->save();
            
            Log::info('Lecturer status toggled', [
                'lecturer_id' => $id,
                'staff_id' => $lecturer->staff_id,
                'previous_status' => $previousStatus,
                'new_status' => $lecturer->is_active,
                'toggled_by' => auth('superadmin')->user()->id ?? 'system'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $lecturer->is_active 
                    ? 'Lecturer enabled successfully' 
                    : 'Lecturer disabled successfully',
                'is_active' => $lecturer->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to toggle lecturer status', [
                'lecturer_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle lecturer status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk toggle students
     */
    public function bulkToggleStudents(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'action' => 'required|in:enable,disable'
        ]);

        try {
            $action = $request->action === 'enable';
            $studentIds = $request->student_ids;
            
            $count = Student::whereIn('id', $studentIds)
                ->update(['is_active' => $action]);
            
            Log::info('Bulk student status toggled', [
                'student_ids' => $studentIds,
                'action' => $request->action,
                'count' => $count,
                'toggled_by' => auth('superadmin')->user()->id ?? 'system'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "$count student(s) " . ($action ? 'enabled' : 'disabled') . " successfully",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to bulk toggle students', [
                'error' => $e->getMessage(),
                'student_ids' => $request->student_ids ?? []
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk toggle students: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk toggle lecturers
     */
    public function bulkToggleLecturers(Request $request)
    {
        $request->validate([
            'lecturer_ids' => 'required|array',
            'lecturer_ids.*' => 'exists:lecturers,id',
            'action' => 'required|in:enable,disable'
        ]);

        try {
            $action = $request->action === 'enable';
            $lecturerIds = $request->lecturer_ids;
            
            $count = Lecturer::whereIn('id', $lecturerIds)
                ->update(['is_active' => $action]);
            
            Log::info('Bulk lecturer status toggled', [
                'lecturer_ids' => $lecturerIds,
                'action' => $request->action,
                'count' => $count,
                'toggled_by' => auth('superadmin')->user()->id ?? 'system'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "$count lecturer(s) " . ($action ? 'enabled' : 'disabled') . " successfully",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to bulk toggle lecturers', [
                'error' => $e->getMessage(),
                'lecturer_ids' => $request->lecturer_ids ?? []
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk toggle lecturers: ' . $e->getMessage()
            ], 500);
        }
    }
}
