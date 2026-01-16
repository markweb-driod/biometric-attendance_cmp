<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Lecturer;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\AcademicLevel;
use App\Models\Department;
use App\Services\AttendanceCalculationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportGenerationService
{
    private AttendanceCalculationService $attendanceService;

    public function __construct(AttendanceCalculationService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Generate attendance report for a department
     *
     * @param int $departmentId
     * @param array $filters
     * @return array
     */
    public function generateAttendanceReport(int $departmentId, array $filters = []): array
    {
        $department = Department::findOrFail($departmentId);
        
        $query = Student::where('department_id', $departmentId)
            ->with(['academicLevel', 'user']);

        // Apply filters
        if (!empty($filters['course_id'])) {
            $query->whereHas('classrooms', function ($q) use ($filters) {
                $q->where('course_id', $filters['course_id']);
            });
        }

        if (!empty($filters['level_id'])) {
            $query->where('academic_level_id', $filters['level_id']);
        }

        if (!empty($filters['lecturer_id'])) {
            $query->whereHas('classrooms.lecturer', function ($q) use ($filters) {
                $q->where('id', $filters['lecturer_id']);
            });
        }

        $students = $query->get();
        
        $reportData = [];
        $totalStudents = $students->count();
        $attendanceStats = [
            'excellent' => 0,
            'good' => 0,
            'warning' => 0,
            'critical' => 0,
        ];

        foreach ($students as $student) {
            $courseId = !empty($filters['course_id']) ? $filters['course_id'] : null;
            $attendance = $this->attendanceService->calculateStudentAttendance($student->id, $courseId);
            
            $attendanceStats[$attendance['status']]++;
            
            $reportData[] = [
                'matric_number' => $student->matric_number,
                'full_name' => $student->user->name ?? $student->full_name,
                'level' => $student->academicLevel->name ?? 'N/A',
                'total_sessions' => $attendance['total_sessions'],
                'attended_sessions' => $attendance['total_present'],
                'attendance_percentage' => $attendance['percentage'],
                'status' => $attendance['status'],
                'status_label' => $this->getStatusLabel($attendance['status']),
            ];
        }

        // Sort by attendance percentage (highest first)
        usort($reportData, function ($a, $b) {
            return $b['attendance_percentage'] <=> $a['attendance_percentage'];
        });

        return [
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
            ],
            'filters' => $filters,
            'summary' => [
                'total_students' => $totalStudents,
                'average_attendance' => $totalStudents > 0 ? 
                    round(collect($reportData)->avg('attendance_percentage'), 2) : 0,
                'distribution' => $attendanceStats,
            ],
            'students' => $reportData,
            'generated_at' => now(),
            'generated_by' => auth()->user()->name ?? 'System',
        ];
    }

    /**
     * Generate performance report for lecturers
     *
     * @param int $departmentId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function generatePerformanceReport(int $departmentId, Carbon $startDate, Carbon $endDate): array
    {
        $department = Department::findOrFail($departmentId);
        $lecturers = Lecturer::where('department_id', $departmentId)
            ->with(['user', 'classrooms.course'])
            ->get();

        $reportData = [];

        foreach ($lecturers as $lecturer) {
            $sessions = AttendanceSession::whereHas('classroom', function ($query) use ($lecturer) {
                $query->where('lecturer_id', $lecturer->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

            $totalSessions = $sessions->count();
            $completedSessions = $sessions->whereNotNull('end_time')->count();
            $punctualSessions = $sessions->where('is_punctual', true)->count();
            $outOfBoundsSessions = $sessions->where('is_out_of_bounds', true)->count();

            // Calculate average session duration
            $avgDuration = $sessions->whereNotNull('end_time')
                ->map(function ($session) {
                    return $session->created_at->diffInMinutes($session->end_time);
                })
                ->avg();

            // Calculate total attendance marked
            $totalAttendance = Attendance::whereIn('attendance_session_id', $sessions->pluck('id'))->count();

            // Calculate courses taught
            $coursesTaught = $lecturer->classrooms->pluck('course.name')->unique()->count();

            $reportData[] = [
                'lecturer_id' => $lecturer->id,
                'staff_id' => $lecturer->staff_id,
                'name' => $lecturer->user->name ?? 'Unknown',
                'total_sessions' => $totalSessions,
                'completed_sessions' => $completedSessions,
                'completion_rate' => $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100, 2) : 0,
                'punctual_sessions' => $punctualSessions,
                'punctuality_rate' => $totalSessions > 0 ? round(($punctualSessions / $totalSessions) * 100, 2) : 0,
                'out_of_bounds_sessions' => $outOfBoundsSessions,
                'geofence_compliance' => $totalSessions > 0 ? round((($totalSessions - $outOfBoundsSessions) / $totalSessions) * 100, 2) : 100,
                'average_duration_minutes' => round($avgDuration ?? 0, 1),
                'total_attendance_marked' => $totalAttendance,
                'courses_taught' => $coursesTaught,
                'teaching_frequency' => $this->calculateTeachingFrequency($totalSessions, $startDate, $endDate),
            ];
        }

        // Sort by total sessions (most active first)
        usort($reportData, function ($a, $b) {
            return $b['total_sessions'] <=> $a['total_sessions'];
        });

        return [
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
            ],
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
            'summary' => [
                'total_lecturers' => $lecturers->count(),
                'total_sessions' => collect($reportData)->sum('total_sessions'),
                'average_completion_rate' => round(collect($reportData)->avg('completion_rate'), 2),
                'average_punctuality_rate' => round(collect($reportData)->avg('punctuality_rate'), 2),
                'average_geofence_compliance' => round(collect($reportData)->avg('geofence_compliance'), 2),
            ],
            'lecturers' => $reportData,
            'generated_at' => now(),
            'generated_by' => auth()->user()->name ?? 'System',
        ];
    }

    /**
     * Export report to PDF format
     *
     * @param array $reportData
     * @param string $reportType
     * @param string $filename
     * @return string File path
     */
    public function exportToPDF(array $reportData, string $reportType, string $filename = null): string
    {
        $filename = $filename ?? $this->generateFilename($reportType, 'pdf');
        
        $viewName = $this->getReportViewName($reportType);
        
        $pdf = Pdf::loadView($viewName, [
            'data' => $reportData,
            'reportType' => $reportType,
        ]);

        $pdf->setPaper('A4', 'portrait');
        
        $filePath = "reports/pdf/{$filename}";
        Storage::disk('public')->put($filePath, $pdf->output());

        return $filePath;
    }

    /**
     * Export report to Excel format using Maatwebsite/Excel
     *
     * @param array $reportData
     * @param string $reportType
     * @param string $filename
     * @return string File path
     */
    public function exportToExcel(array $reportData, string $reportType, string $filename = null): string
    {
        $filename = $filename ?? $this->generateFilename($reportType, 'xlsx');
        
        $exportClass = $this->getExportClass($reportType);
        
        $filePath = "reports/excel/{$filename}";
        
        Excel::store(new $exportClass($reportData), $filePath, 'public');

        return $filePath;
    }

    /**
     * Export report to CSV format
     *
     * @param array $reportData
     * @param string $reportType
     * @param string $filename
     * @return string File path
     */
    public function exportToCSV(array $reportData, string $reportType, string $filename = null): string
    {
        $filename = $filename ?? $this->generateFilename($reportType, 'csv');
        
        $filePath = "reports/csv/{$filename}";
        
        $csvData = $this->convertToCSV($reportData, $reportType);
        
        Storage::disk('public')->put($filePath, $csvData);

        return $filePath;
    }

    /**
     * Generate compliance report
     *
     * @param int $departmentId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param float $attendanceThreshold
     * @return array
     */
    public function generateComplianceReport(
        int $departmentId, 
        Carbon $startDate, 
        Carbon $endDate, 
        float $attendanceThreshold = 75.0
    ): array {
        $department = Department::findOrFail($departmentId);
        
        // Get all students in department
        $students = Student::where('department_id', $departmentId)
            ->with(['academicLevel', 'user'])
            ->get();

        $complianceData = [];
        $summary = [
            'total_students' => $students->count(),
            'compliant_students' => 0,
            'non_compliant_students' => 0,
            'compliance_rate' => 0,
            'threshold' => $attendanceThreshold,
        ];

        foreach ($students as $student) {
            $attendance = $this->attendanceService->calculateStudentAttendance($student->id);
            $isCompliant = $attendance['percentage'] >= $attendanceThreshold;
            
            if ($isCompliant) {
                $summary['compliant_students']++;
            } else {
                $summary['non_compliant_students']++;
            }

            $complianceData[] = [
                'matric_number' => $student->matric_number,
                'full_name' => $student->user->name ?? $student->full_name,
                'level' => $student->academicLevel->name ?? 'N/A',
                'attendance_percentage' => $attendance['percentage'],
                'is_compliant' => $isCompliant,
                'compliance_status' => $isCompliant ? 'Compliant' : 'Non-Compliant',
                'sessions_attended' => $attendance['total_present'],
                'total_sessions' => $attendance['total_sessions'],
                'sessions_missed' => $attendance['total_sessions'] - $attendance['total_present'],
            ];
        }

        $summary['compliance_rate'] = $summary['total_students'] > 0 
            ? round(($summary['compliant_students'] / $summary['total_students']) * 100, 2) 
            : 0;

        // Sort by compliance status (non-compliant first) then by attendance percentage
        usort($complianceData, function ($a, $b) {
            if ($a['is_compliant'] !== $b['is_compliant']) {
                return $a['is_compliant'] <=> $b['is_compliant'];
            }
            return $b['attendance_percentage'] <=> $a['attendance_percentage'];
        });

        return [
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
            ],
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'summary' => $summary,
            'students' => $complianceData,
            'generated_at' => now(),
            'generated_by' => auth()->user()->name ?? 'System',
        ];
    }

    /**
     * Get status label for attendance status
     *
     * @param string $status
     * @return string
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'excellent' => 'Excellent (≥75%)',
            'good' => 'Good (60-74%)',
            'warning' => 'Warning (50-59%)',
            'critical' => 'Critical (<50%)',
            default => 'Unknown',
        };
    }

    /**
     * Calculate teaching frequency
     *
     * @param int $totalSessions
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return float
     */
    private function calculateTeachingFrequency(int $totalSessions, Carbon $startDate, Carbon $endDate): float
    {
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $workingDays = $this->calculateWorkingDays($startDate, $endDate);
        
        return $workingDays > 0 ? round($totalSessions / $workingDays, 2) : 0;
    }

    /**
     * Calculate working days (excluding weekends)
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    private function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $workingDays = 0;
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            if ($current->isWeekday()) {
                $workingDays++;
            }
            $current->addDay();
        }
        
        return $workingDays;
    }

    /**
     * Generate filename for reports
     *
     * @param string $reportType
     * @param string $extension
     * @return string
     */
    private function generateFilename(string $reportType, string $extension): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        return "{$reportType}_report_{$timestamp}.{$extension}";
    }

    /**
     * Get view name for PDF reports
     *
     * @param string $reportType
     * @return string
     */
    private function getReportViewName(string $reportType): string
    {
        return match ($reportType) {
            'attendance' => 'hod.reports.pdf.attendance',
            'performance' => 'hod.reports.pdf.performance',
            'compliance' => 'hod.reports.pdf.compliance',
            default => 'hod.reports.pdf.generic',
        };
    }

    /**
     * Get export class for Excel reports
     *
     * @param string $reportType
     * @return string
     */
    private function getExportClass(string $reportType): string
    {
        return match ($reportType) {
            'attendance' => \App\Exports\AttendanceReportExport::class,
            'performance' => \App\Exports\PerformanceReportExport::class,
            'compliance' => \App\Exports\ComplianceReportExport::class,
            default => \App\Exports\GenericReportExport::class,
        };
    }

    /**
     * Convert report data to CSV format
     *
     * @param array $reportData
     * @param string $reportType
     * @return string
     */
    private function convertToCSV(array $reportData, string $reportType): string
    {
        $output = fopen('php://temp', 'r+');
        
        switch ($reportType) {
            case 'attendance':
                fputcsv($output, [
                    'Matric Number', 'Full Name', 'Level', 'Total Sessions', 
                    'Attended Sessions', 'Attendance %', 'Status'
                ]);
                
                foreach ($reportData['students'] as $student) {
                    fputcsv($output, [
                        $student['matric_number'],
                        $student['full_name'],
                        $student['level'],
                        $student['total_sessions'],
                        $student['attended_sessions'],
                        $student['attendance_percentage'],
                        $student['status_label'],
                    ]);
                }
                break;
                
            case 'performance':
                fputcsv($output, [
                    'Staff ID', 'Name', 'Total Sessions', 'Completion Rate %', 
                    'Punctuality Rate %', 'Geofence Compliance %', 'Avg Duration (min)'
                ]);
                
                foreach ($reportData['lecturers'] as $lecturer) {
                    fputcsv($output, [
                        $lecturer['staff_id'],
                        $lecturer['name'],
                        $lecturer['total_sessions'],
                        $lecturer['completion_rate'],
                        $lecturer['punctuality_rate'],
                        $lecturer['geofence_compliance'],
                        $lecturer['average_duration_minutes'],
                    ]);
                }
                break;
                
            case 'compliance':
                fputcsv($output, [
                    'Matric Number', 'Full Name', 'Level', 'Attendance %', 
                    'Compliance Status', 'Sessions Attended', 'Total Sessions'
                ]);
                
                foreach ($reportData['students'] as $student) {
                    fputcsv($output, [
                        $student['matric_number'],
                        $student['full_name'],
                        $student['level'],
                        $student['attendance_percentage'],
                        $student['compliance_status'],
                        $student['sessions_attended'],
                        $student['total_sessions'],
                    ]);
                }
                break;
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}