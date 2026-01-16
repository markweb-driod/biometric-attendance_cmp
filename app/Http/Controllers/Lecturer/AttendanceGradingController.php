<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Services\AttendanceCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceGradingController extends Controller
{
    /**
     * List classrooms for grading.
     */
    public function index()
    {
        $classrooms = Classroom::where('is_active', true)->get();
        return view('lecturer.grading.index', compact('classrooms'));
    }

    /**
     * Show grading page for a classroom.
     */
    public function show(Classroom $classroom)
    {
        // Get grading rules or default
        $defaultRules = ['A' => 75, 'B' => 60, 'C' => 50, 'D' => 45, 'F' => 0];
        $rules = $classroom->grading_rules ?? $defaultRules;

        $service = new AttendanceCalculationService();
        $students = $classroom->students()->with(['user'])->get();
        $grades = [];
        foreach ($students as $student) {
            $attendance = $service->calculateStudentAttendance($student->id);
            $grade = $service->calculateGrade($attendance['percentage'], $rules);
            $grades[] = [
                'student' => $student,
                'attendance' => $attendance,
                'grade' => $grade,
            ];
        }
        return view('lecturer.grading.show', compact('classroom', 'grades', 'rules'));
    }

    /**
     * Update grading rules for a classroom.
     */
    public function updateRules(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'rules' => 'required|array',
        ]);
        $classroom->grading_rules = $validated['rules'];
        $classroom->save();
        return redirect()->back()->with('success', 'Grading rules updated.');
    }

    /**
     * Export grading report as CSV.
     */
    public function export(Classroom $classroom)
    {
        $defaultRules = ['A' => 75, 'B' => 60, 'C' => 50, 'D' => 45, 'F' => 0];
        $rules = $classroom->grading_rules ?? $defaultRules;
        $service = new AttendanceCalculationService();
        $students = $classroom->students()->with(['user'])->get();

        $callback = function () use ($students, $service, $rules) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Matric Number', 'Full Name', 'Attendance %', 'Grade']);
            foreach ($students as $student) {
                $attendance = $service->calculateStudentAttendance($student->id);
                $grade = $service->calculateGrade($attendance['percentage'], $rules);
                fputcsv($handle, [
                    $student->matric_number,
                    $student->user->full_name ?? 'N/A',
                    $attendance['percentage'],
                    $grade,
                ]);
            }
            fclose($handle);
        };
        $filename = 'grading_report_' . $classroom->id . '.csv';
        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    /**
     * Get grade distribution data for chart.
     */
    public function getGradeDistribution(Classroom $classroom)
    {
        $defaultRules = ['A' => 75, 'B' => 60, 'C' => 50, 'D' => 45, 'F' => 0];
        $rules = $classroom->grading_rules ?? $defaultRules;
        $service = new AttendanceCalculationService();
        $students = $classroom->students()->with(['user'])->get();
        
        $distribution = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0];
        
        foreach ($students as $student) {
            $attendance = $service->calculateStudentAttendance($student->id);
            $grade = $service->calculateGrade($attendance['percentage'], $rules);
            if (isset($distribution[$grade])) {
                $distribution[$grade]++;
            }
        }
        
        return response()->json([
            'labels' => array_keys($distribution),
            'data' => array_values($distribution),
            'total' => $students->count()
        ]);
    }

    /**
     * Handle bulk grade override.
     */
    public function bulkOverride(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'grade' => 'required|in:A,B,C,D,F',
            'reason' => 'required|string|max:500',
        ]);

        $lecturer = Auth::guard('lecturer')->user();
        
        foreach ($validated['student_ids'] as $studentId) {
            $classroom->students()->updateExistingPivot($studentId, [
                'grade_override' => $validated['grade'],
                'override_reason' => $validated['reason'],
                'overridden_by' => $lecturer->id,
                'overridden_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Grades overridden for ' . count($validated['student_ids']) . ' student(s).');
    }

    /**
     * Send grade notifications to students.
     */
    public function sendNotifications(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $defaultRules = ['A' => 75, 'B' => 60, 'C' => 50, 'D' => 45, 'F' => 0];
        $rules = $classroom->grading_rules ?? $defaultRules;
        $service = new AttendanceCalculationService();
        
        $sentCount = 0;
        foreach ($validated['student_ids'] as $studentId) {
            $student = Student::with('user')->find($studentId);
            if ($student && $student->user && $student->user->email) {
                $attendance = $service->calculateStudentAttendance($student->id);
                $grade = $service->calculateGrade($attendance['percentage'], $rules);
                
                try {
                    Mail::send('emails.grade_notification', [
                        'student' => $student,
                        'classroom' => $classroom,
                        'attendance' => $attendance,
                        'grade' => $grade,
                    ], function ($message) use ($student) {
                        $message->to($student->user->email)
                                ->subject('Attendance Grade Notification');
                    });
                    $sentCount++;
                } catch (\Exception $e) {
                    // Log error but continue
                    \Log::error('Failed to send grade notification: ' . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('success', "Grade notifications sent to {$sentCount} student(s).");
    }

    /**
     * Show printable PDF view.
     */
    public function exportPdf(Classroom $classroom)
    {
        $defaultRules = ['A' => 75, 'B' => 60, 'C' => 50, 'D' => 45, 'F' => 0];
        $rules = $classroom->grading_rules ?? $defaultRules;
        $service = new AttendanceCalculationService();
        $students = $classroom->students()->with(['user'])->get();
        $grades = [];
        
        foreach ($students as $student) {
            $attendance = $service->calculateStudentAttendance($student->id);
            $grade = $service->calculateGrade($attendance['percentage'], $rules);
            $grades[] = [
                'student' => $student,
                'attendance' => $attendance,
                'grade' => $grade,
            ];
        }
        
        return view('lecturer.grading.pdf', compact('classroom', 'grades', 'rules'));
    }
}
