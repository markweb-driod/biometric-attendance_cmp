<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceCalculationService
{
    /**
     * Calculate attendance percentage for a specific student
     *
     * @param int $studentId
     * @param int|null $courseId Optional course filter
     * @return array
     */
    public function calculateStudentAttendance(int $studentId, ?int $courseId = null): array
    {
        $query = Attendance::where('student_id', $studentId);

        if ($courseId) {
            $query->whereHas('attendanceSession.classroom', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $totalPresent = $query->count();

        // Get total sessions the student should have attended
        $sessionQuery = AttendanceSession::whereHas('classroom.students', function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        });

        if ($courseId) {
            $sessionQuery->whereHas('classroom', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $totalSessions = $sessionQuery->count();

        $percentage = $totalSessions > 0 ? ($totalPresent / $totalSessions) * 100 : 0;

        return [
            'total_present' => $totalPresent,
            'total_sessions' => $totalSessions,
            'percentage' => round($percentage, 2),
            'status' => $this->getAttendanceStatus($percentage),
        ];
    }

    /**
     * Get students below attendance threshold for a department
     *
     * @param int $departmentId
     * @param float $threshold
     * @return Collection
     */
    public function getStudentsBelowThreshold(int $departmentId, float $threshold = 75.0): Collection
    {
        $students = Student::where('department_id', $departmentId)
            ->where('is_active', true)
            ->get();
        $belowThreshold = collect();

        foreach ($students as $student) {
            $attendance = $this->calculateStudentAttendance($student->id);
            if ($attendance['percentage'] < $threshold) {
                $student->attendance_data = $attendance;
                $belowThreshold->push($student);
            }
        }

        return $belowThreshold->sortBy('attendance_data.percentage');
    }

    /**
     * Get attendance status based on percentage
     *
     * @param float $percentage
     * @return string
     */
    public function getAttendanceStatus(float $percentage): string
    {
        if ($percentage >= 75) {
            return 'excellent';
        } elseif ($percentage >= 60) {
            return 'good';
        } elseif ($percentage >= 50) {
            return 'warning';
        } else {
            return 'critical';
        }
    }

    /**
     * Calculate grade based on attendance percentage and grading rules.
     *
     * @param float $percentage
     * @param array $rules Associative array of grade thresholds, e.g. ['A'=>75,'B'=>60,'C'=>50,'D'=>45,'F'=>0]
     * @return string Grade letter
     */
    public function calculateGrade(float $percentage, array $rules): string
    {
        arsort($rules);
        foreach ($rules as $grade => $threshold) {
            if ($percentage >= $threshold) {
                return $grade;
            }
        }
        return 'F';
    }

    /**
     * Calculate attendance statistics for multiple students
     *
     * @param Collection $students
     * @param int|null $courseId
     * @return array
     */
    public function calculateBulkAttendance(Collection $students, ?int $courseId = null): array
    {
        $results = [];

        foreach ($students as $student) {
            $results[$student->id] = $this->calculateStudentAttendance($student->id, $courseId);
        }

        return $results;
    }

    /**
     * Get attendance summary for a department
     *
     * @param int $departmentId
     * @return array
     */
    public function getDepartmentAttendanceSummary(int $departmentId): array
    {
        $students = Student::where('department_id', $departmentId)
            ->where('is_active', true)
            ->get();
        $totalStudents = $students->count();

        if ($totalStudents === 0) {
            return [
                'total_students' => 0,
                'average_attendance' => 0,
                'excellent_count' => 0,
                'good_count' => 0,
                'warning_count' => 0,
                'critical_count' => 0,
            ];
        }

        $statusCounts = [
            'excellent' => 0,
            'good' => 0,
            'warning' => 0,
            'critical' => 0,
        ];

        $totalPercentage = 0;

        foreach ($students as $student) {
            $attendance = $this->calculateStudentAttendance($student->id);
            $totalPercentage += $attendance['percentage'];
            $statusCounts[$attendance['status']]++;
        }

        return [
            'total_students' => $totalStudents,
            'average_attendance' => round($totalPercentage / $totalStudents, 2),
            'excellent_count' => $statusCounts['excellent'],
            'good_count' => $statusCounts['good'],
            'warning_count' => $statusCounts['warning'],
            'critical_count' => $statusCounts['critical'],
        ];
    }
}