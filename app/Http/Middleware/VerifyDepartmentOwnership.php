<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyDepartmentOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hod = auth('hod')->user();
        
        if (!$hod) {
            return redirect('/login');
        }

        // Get department ID from route parameters or request
        $departmentId = $request->route('departmentId') ?? $request->input('department_id');
        
        if ($departmentId && $hod->department_id != $departmentId) {
            abort(403, 'You do not have permission to access resources from this department.');
        }

        // For student/lecturer resources, verify they belong to HOD's department
        $studentId = $request->route('studentId') ?? $request->input('student_id');
        $lecturerId = $request->route('lecturerId') ?? $request->input('lecturer_id');
        
        if ($studentId) {
            $student = \App\Models\Student::find($studentId);
            if ($student && $student->department_id != $hod->department_id) {
                abort(403, 'You do not have permission to access this student.');
            }
        }
        
        if ($lecturerId) {
            $lecturer = \App\Models\Lecturer::find($lecturerId);
            if ($lecturer && $lecturer->department_id != $hod->department_id) {
                abort(403, 'You do not have permission to access this lecturer.');
            }
        }

        return $next($request);
    }
}