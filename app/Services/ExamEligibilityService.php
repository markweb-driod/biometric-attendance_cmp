<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ExamEligibility;
use App\Models\AuditLog;
use App\Services\AttendanceCalculationService;
use App\Events\ExamEligibilityUpdated;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExamEligibilityService
{
    private AttendanceCalculationService $attendanceService;

    public function __construct(AttendanceCalculationService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Validate exam eligibility for all students in a department
     *
     * @param int $departmentId
     * @param float $threshold
     * @param string|null $semester
     * @param string|null $academicYear
     * @return array
     */
    public function validateEligibility(
        int $departmentId, 
        float $threshold = 75.0, 
        ?string $semester = null, 
        ?string $academicYear = null
    ): array {
        $semester = $semester ?? $this->getCurrentSemester();
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();

        $students = Student::where('department_id', $departmentId)->get();
        $results = [
            'eligible' => 0,
            'ineligible' => 0,
            'updated' => [],
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            foreach ($students as $student) {
                try {
                    // Get all courses for the student's department
                    $courses = \App\Models\Course::whereHas('classrooms.students', function($q) use ($student) {
                        $q->where('student_id', $student->id);
                    })->get();

                    foreach ($courses as $course) {
                        $courseAttendance = $this->attendanceService->calculateStudentAttendance($student->id, $course->id);
                        $courseEligible = $courseAttendance['percentage'] >= $threshold;

                        ExamEligibility::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'course_id' => $course->id,
                                'semester' => $semester,
                                'academic_year' => $academicYear,
                            ],
                            [
                                'attendance_percentage' => $courseAttendance['percentage'],
                                'required_threshold' => $threshold,
                                'status' => $courseEligible ? 'eligible' : 'ineligible',
                                'validated_at' => now(),
                                'overridden_by' => null,
                                'override_reason' => null,
                            ]
                        );
                        
                        // Count per course
                        $courseEligible ? $results['eligible']++ : $results['ineligible']++;
                    }
                    $results['updated'][] = [
                        'student_id' => $student->id,
                        'matric_number' => $student->matric_number,
                        'attendance_percentage' => $attendance['percentage'],
                        'is_eligible' => $isEligible,
                    ];

                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'student_id' => $student->id,
                        'matric_number' => $student->matric_number,
                        'error' => $e->getMessage(),
                    ];
                    
                    Log::error('Failed to validate eligibility for student', [
                        'student_id' => $student->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();

            // Create audit log
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_type' => auth()->user() ? get_class(auth()->user()) : null,
                'resource_type' => ExamEligibility::class,
                'action' => 'validate_exam_eligibility',
                'description' => "Validated exam eligibility for {$results['eligible']} eligible and {$results['ineligible']} ineligible students",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent() ?? 'system',
                'department_id' => $departmentId,
                'severity' => 'low',
                'old_values' => null,
                'new_values' => [
                    'department_id' => $departmentId,
                    'threshold' => $threshold,
                    'semester' => $semester,
                    'academic_year' => $academicYear,
                    'total_processed' => count($results['updated']),
                    'errors_count' => count($results['errors']),
                ],
            ]);

            // Fire event for real-time updates (if event class exists)
            if (class_exists('\App\Events\ExamEligibilityUpdated')) {
                event(new ExamEligibilityUpdated($departmentId, $results));
            }

            return $results;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to validate exam eligibility', [
                'department_id' => $departmentId,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to validate exam eligibility: ' . $e->getMessage());
        }
    }

    /**
     * Override exam eligibility for a specific student (updated signature for controller compatibility)
     *
     * @param int $studentId
     * @param int $courseId
     * @param string $semester
     * @param string $academicYear
     * @param string $overrideReason
     * @param string $status
     * @param int $hodId
     * @return array
     */
    public function overrideEligibility(
        int $studentId,
        int $courseId,
        string $semester,
        string $academicYear,
        string $overrideReason,
        string $status,
        int $hodId
    ): array {
        $student = Student::findOrFail($studentId);
        $isEligible = $status === 'eligible';
        
        $attendance = $this->attendanceService->calculateStudentAttendance($studentId);

        $eligibility = ExamEligibility::updateOrCreate(
            [
                'student_id' => $studentId,
                'semester' => $semester,
                'academic_year' => $academicYear,
            ],
            [
                'attendance_percentage' => $attendance['percentage'],
                'status' => 'overridden',
                'overridden_by' => $hodId,
                'override_reason' => $overrideReason,
                'validated_at' => now(),
                'overridden_at' => now(),
            ]
        );

        // Create audit log for override
        AuditLog::create([
            'user_id' => $hodId,
            'user_type' => 'App\Models\Hod',
            'role' => 'hod',
            'action' => 'override_exam_eligibility',
            'description' => "Overrode exam eligibility for student {$student->matric_number} to {$status}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'success',
            'metadata' => [
                'student_id' => $studentId,
                'matric_number' => $student->matric_number,
                'original_attendance' => $attendance['percentage'],
                'override_to' => $isEligible,
                'reason' => $overrideReason,
                'semester' => $semester,
                'academic_year' => $academicYear,
            ],
        ]);

        Log::info('Exam eligibility overridden', [
            'student_id' => $studentId,
            'hod_id' => $hodId,
            'status' => $status,
            'reason' => $overrideReason,
        ]);

        return [
            'success' => true,
            'message' => 'Eligibility overridden successfully',
            'data' => $eligibility
        ];
    }

    /**
     * Original override method for backward compatibility
     *
     * @param int $studentId
     * @param bool $isEligible
     * @param string $reason
     * @param int $hodId
     * @param string|null $semester
     * @param string|null $academicYear
     * @return ExamEligibility
     */
    public function overrideEligibilityOriginal(
        int $studentId,
        bool $isEligible,
        string $reason,
        int $hodId,
        ?string $semester = null,
        ?string $academicYear = null
    ): ExamEligibility {
        $semester = $semester ?? $this->getCurrentSemester();
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();

        $student = Student::findOrFail($studentId);
        $attendance = $this->attendanceService->calculateStudentAttendance($studentId);

        $eligibility = ExamEligibility::updateOrCreate(
            [
                'student_id' => $studentId,
                'semester' => $semester,
                'academic_year' => $academicYear,
            ],
            [
                'attendance_percentage' => $attendance['percentage'],
                'status' => 'overridden',
                'overridden_by' => $hodId,
                'override_reason' => $reason,
                'validated_at' => now(),
                'overridden_at' => now(),
            ]
        );

        // Create audit log for override
        AuditLog::create([
            'user_id' => $hodId,
            'user_type' => 'App\Models\HOD',
            'role' => 'hod',
            'action' => 'override_exam_eligibility',
            'description' => "Overrode exam eligibility for student {$student->matric_number} to " . ($isEligible ? 'eligible' : 'ineligible'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'success',
            'metadata' => [
                'student_id' => $studentId,
                'matric_number' => $student->matric_number,
                'original_attendance' => $attendance['percentage'],
                'override_to' => $isEligible,
                'reason' => $reason,
                'semester' => $semester,
                'academic_year' => $academicYear,
            ],
        ]);

        Log::info('Exam eligibility overridden', [
            'student_id' => $studentId,
            'hod_id' => $hodId,
            'is_eligible' => $isEligible,
            'reason' => $reason,
        ]);

        return $eligibility;
    }

    /**
     * Get current semester based on date
     *
     * @return string
     */
    public function getCurrentSemester(): string
    {
        $month = now()->month;
        
        // Assuming academic calendar:
        // September - January: First Semester
        // February - June: Second Semester
        // July - August: Break/Summer
        
        if ($month >= 9 || $month <= 1) {
            return 'First';
        } elseif ($month >= 2 && $month <= 6) {
            return 'Second';
        } else {
            return 'Summer';
        }
    }

    /**
     * Get current academic year
     *
     * @return string
     */
    public function getCurrentAcademicYear(): string
    {
        $currentYear = now()->year;
        $month = now()->month;
        
        // Academic year starts in September
        if ($month >= 9) {
            return $currentYear . '/' . ($currentYear + 1);
        } else {
            return ($currentYear - 1) . '/' . $currentYear;
        }
    }

    /**
     * Get eligibility status for students in a department
     *
     * @param int $departmentId
     * @param string|null $semester
     * @param string|null $academicYear
     * @return Collection
     */
    public function getEligibilityStatus(
        int $departmentId,
        ?string $semester = null,
        ?string $academicYear = null
    ): Collection {
        $semester = $semester ?? $this->getCurrentSemester();
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();

        return ExamEligibility::whereHas('student', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->where('semester', $semester)
        ->where('academic_year', $academicYear)
        ->with(['student', 'overriddenBy'])
        ->orderBy('status', 'desc')
        ->orderBy('attendance_percentage', 'desc')
        ->get();
    }

    /**
     * Generate clearance list for eligible students
     *
     * @param int $departmentId
     * @param string|null $semester
     * @param string|null $academicYear
     * @return array
     */
    public function generateClearanceList(
        int $departmentId,
        ?string $semester = null,
        ?string $academicYear = null
    ): array {
        $semester = $semester ?? $this->getCurrentSemester();
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();

        $eligibleStudents = ExamEligibility::whereHas('student', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->where('semester', $semester)
        ->where('academic_year', $academicYear)
        ->where('status', 'eligible')
        ->with(['student.department', 'student.academicLevel'])
        ->orderBy('attendance_percentage', 'desc')
        ->get();

        return [
            'department_id' => $departmentId,
            'semester' => $semester,
            'academic_year' => $academicYear,
            'total_eligible' => $eligibleStudents->count(),
            'generated_at' => now(),
            'students' => $eligibleStudents->map(function ($eligibility) {
                return [
                    'matric_number' => $eligibility->student->matric_number,
                    'full_name' => $eligibility->student->full_name,
                    'level' => $eligibility->student->academicLevel->name ?? 'N/A',
                    'attendance_percentage' => $eligibility->attendance_percentage,
                    'is_override' => !is_null($eligibility->override_by),
                    'override_reason' => $eligibility->override_reason,
                ];
            })->toArray(),
        ];
    }

    /**
     * Get eligibility statistics for a department
     *
     * @param int $departmentId
     * @param string|null $semester
     * @param string|null $academicYear
     * @return array
     */
    public function getEligibilityStatistics(
        int $departmentId,
        ?string $semester = null,
        ?string $academicYear = null
    ): array {
        $semester = $semester ?? $this->getCurrentSemester();
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();

        $stats = ExamEligibility::whereHas('student', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->where('semester', $semester)
        ->where('academic_year', $academicYear)
        ->selectRaw('
            COUNT(*) as total_students,
            SUM(CASE WHEN status = "eligible" OR status = "overridden" THEN 1 ELSE 0 END) as eligible_count,
            SUM(CASE WHEN status = "ineligible" THEN 1 ELSE 0 END) as ineligible_count,
            SUM(CASE WHEN overridden_by IS NOT NULL THEN 1 ELSE 0 END) as override_count,
            AVG(attendance_percentage) as average_attendance
        ')
        ->first();

        $eligibilityRate = $stats->total_students > 0 
            ? ($stats->eligible_count / $stats->total_students) * 100 
            : 0;

        return [
            'total_students' => $stats->total_students ?? 0,
            'eligible_count' => $stats->eligible_count ?? 0,
            'ineligible_count' => $stats->ineligible_count ?? 0,
            'override_count' => $stats->override_count ?? 0,
            'eligibility_rate' => round($eligibilityRate, 2),
            'average_attendance' => round($stats->average_attendance ?? 0, 2),
            'semester' => $semester,
            'academic_year' => $academicYear,
        ];
    }

    /**
     * Check if eligibility validation is needed
     *
     * @param int $departmentId
     * @param string|null $semester
     * @param string|null $academicYear
     * @return bool
     */
    public function isValidationNeeded(
        int $departmentId,
        ?string $semester = null,
        ?string $academicYear = null
    ): bool {
        $semester = $semester ?? $this->getCurrentSemester();
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();

        $totalStudents = Student::where('department_id', $departmentId)->count();
        
        $validatedStudents = ExamEligibility::whereHas('student', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->where('semester', $semester)
        ->where('academic_year', $academicYear)
        ->count();

        return $validatedStudents < $totalStudents;
    }

    /**
     * Get eligibility data with filters for HOD dashboard
     *
     * @param int $departmentId
     * @param array $filters
     * @return array
     */
    public function getEligibilityData(int $departmentId, array $filters = []): array
    {
        $semester = $filters['semester'] ?? $this->getCurrentSemester();
        $academicYear = $filters['academic_year'] ?? $this->getCurrentAcademicYear();
        $levelId = $filters['level_id'] ?? null;
        $status = $filters['status'] ?? null;
        $search = $filters['search'] ?? null;

        $query = ExamEligibility::whereHas('student', function ($q) use ($departmentId, $levelId) {
            $q->where('department_id', $departmentId);
            if ($levelId) {
                $q->where('academic_level_id', $levelId);
            }
        })
        ->where('semester', $semester)
        ->where('academic_year', $academicYear)
        ->with(['student.academicLevel']);

        // Apply additional filters
        if ($status) {
            if ($status === 'overridden') {
                $query->whereNotNull('overridden_by');
            } else {
                $query->where('status', $status === 'eligible' ? 'eligible' : 'ineligible');
            }
        }

        // Search filter
        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('matric_number', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'student.full_name';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        
        if ($sortBy === 'student_name') {
            $query->join('students', 'exam_eligibilities.student_id', '=', 'students.id')
                  ->orderBy('students.full_name', $sortOrder)
                  ->select('exam_eligibilities.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $filters['per_page'] ?? 25;
        $eligibilities = $query->paginate($perPage);

        return [
            'data' => $eligibilities->map(function ($eligibility) {
                return [
                    'id' => $eligibility->id,
                    'student_id' => $eligibility->student_id,
                    'matric_number' => $eligibility->student->matric_number,
                    'full_name' => $eligibility->student->full_name,
                    'academic_level' => $eligibility->student->academicLevel->name ?? 'N/A',
                    'attendance_percentage' => round($eligibility->attendance_percentage, 2),
                    'is_eligible' => in_array($eligibility->status, ['eligible', 'overridden']),
                    'override_by' => $eligibility->overridden_by,
                    'override_reason' => $eligibility->override_reason,
                    'validated_at' => $eligibility->validated_at,
                ];
            }),
            'total' => $eligibilities->total(),
            'current_page' => $eligibilities->currentPage(),
            'per_page' => $eligibilities->perPage(),
        ];
    }

    /**
     * Get eligibility statistics (alias for getEligibilityStatistics)
     *
     * @param int $departmentId
     * @param array $filters
     * @return array
     */
    public function getEligibilityStats(int $departmentId, array $filters = []): array
    {
        $semester = $filters['semester'] ?? $this->getCurrentSemester();
        $academicYear = $filters['academic_year'] ?? $this->getCurrentAcademicYear();

        return $this->getEligibilityStatistics($departmentId, $semester, $academicYear);
    }

    /**
     * Get at-risk students (those with low attendance or overridden status)
     *
     * @param int $departmentId
     * @param array $filters
     * @return array
     */
    public function getAtRiskStudents(int $departmentId, array $filters = []): array
    {
        $semester = $filters['semester'] ?? $this->getCurrentSemester();
        $academicYear = $filters['academic_year'] ?? $this->getCurrentAcademicYear();
        $threshold = $filters['attendance_threshold'] ?? 75;

        $atRiskStudents = ExamEligibility::whereHas('student', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->where('semester', $semester)
        ->where('academic_year', $academicYear)
        ->where(function ($query) use ($threshold) {
            $query->where('attendance_percentage', '<', $threshold)
                  ->orWhere('status', 'ineligible')
                  ->orWhere('status', 'overridden');
        })
        ->with(['student.academicLevel'])
        ->orderBy('attendance_percentage', 'asc')
        ->limit(10)
        ->get();

        return $atRiskStudents->map(function ($eligibility) {
            return [
                'id' => $eligibility->id,
                'student_id' => $eligibility->student_id,
                'matric_number' => $eligibility->student->matric_number,
                'full_name' => $eligibility->student->full_name,
                'academic_level' => $eligibility->student->academicLevel->name ?? 'N/A',
                'attendance_percentage' => round($eligibility->attendance_percentage, 2),
                'is_eligible' => in_array($eligibility->status, ['eligible', 'overridden']),
                'is_override' => !is_null($eligibility->overridden_by),
                'override_reason' => $eligibility->override_reason,
            ];
        })->toArray();
    }

    /**
     * Get recent overrides
     *
     * @param int $departmentId
     * @return array
     */
    public function getRecentOverrides(int $departmentId): array
    {
        $recentOverrides = ExamEligibility::whereHas('student', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->whereNotNull('override_by')
        ->with(['student.academicLevel'])
        ->orderBy('validated_at', 'desc')
        ->limit(10)
        ->get();

        return $recentOverrides->map(function ($eligibility) {
            return [
                'id' => $eligibility->id,
                'student_id' => $eligibility->student_id,
                'matric_number' => $eligibility->student->matric_number,
                'full_name' => $eligibility->student->full_name,
                'semester' => $eligibility->semester,
                'academic_year' => $eligibility->academic_year,
                'attendance_percentage' => round($eligibility->attendance_percentage, 2),
                'is_eligible' => in_array($eligibility->status, ['eligible', 'overridden']),
                'override_reason' => $eligibility->override_reason,
                'validated_at' => $eligibility->validated_at,
            ];
        })->toArray();
    }

    /**
     * Bulk override eligibility
     *
     * @param array $overrides
     * @param int $hodId
     * @return array
     */
    public function bulkOverrideEligibility(array $overrides, int $hodId): array
    {
        $results = [];

        foreach ($overrides as $override) {
            try {
                $result = $this->overrideEligibility(
                    $override['student_id'],
                    $override['course_id'],
                    $override['semester'],
                    $override['academic_year'],
                    $override['override_reason'],
                    $override['status'],
                    $hodId
                );
                $results[] = [
                    'student_id' => $override['student_id'],
                    'success' => true,
                    'message' => 'Override successful'
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'student_id' => $override['student_id'],
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Waive eligibility requirement (requires 2FA)
     *
     * @param int $studentId
     * @param int $courseId
     * @param string $semester
     * @param string $academicYear
     * @param string $reason
     * @param string|null $documentRef
     * @param int $hodId
     * @return array
     */
    public function waiveEligibility(
        int $studentId,
        int $courseId,
        string $semester,
        string $academicYear,
        string $reason,
        ?string $documentRef,
        int $hodId
    ): array {
        try {
            DB::beginTransaction();

            // Verify student exists
            $student = Student::findOrFail($studentId);
            
            // Verify course exists
            $course = Course::findOrFail($courseId);

            // Find or create eligibility record
            $eligibility = ExamEligibility::firstOrCreate(
                [
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'semester' => $semester,
                    'academic_year' => $academicYear
                ],
                [
                    'is_eligible' => false,
                    'attendance_percentage' => 0,
                    'calculated_at' => now()
                ]
            );

            // Update with waiver - use status 'overridden' with special marking
            $eligibility->update([
                'is_eligible' => true,
                'status' => 'overridden', // Using 'overridden' status, but we'll mark it as waived in reason
                'override_reason' => 'WAIVED: ' . $reason . ($documentRef ? ' [Document Ref: ' . $documentRef . ']' : ''),
                'overridden_by' => $hodId,
                'overridden_at' => now(),
            ]);

            // Log audit trail
            AuditLog::create([
                'user_type' => 'hod',
                'user_id' => $hodId,
                'role' => 'hod',
                'action' => 'waive_eligibility',
                'model_type' => ExamEligibility::class,
                'model_id' => $eligibility->id,
                'description' => "Waived eligibility requirement (2FA verified) for student {$student->matric_number} in course {$course->code}. Reason: " . substr($reason, 0, 100),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'success',
                'metadata' => [
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'document_ref' => $documentRef,
                    'waiver_reason' => $reason
                ],
            ]);

            // Fire event (using 'overridden' as event type since status is overridden)
            try {
                event(new ExamEligibilityUpdated($eligibility, 'overridden'));
            } catch (\Exception $e) {
                // Event might not be defined, continue
                Log::warning('Could not fire ExamEligibilityUpdated event', ['error' => $e->getMessage()]);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Eligibility requirement waived successfully',
                'data' => [
                    'eligibility' => $eligibility,
                    'student' => $student,
                    'course' => $course
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error waiving eligibility', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to waive eligibility: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Calculate eligibility for department
     *
     * @param int $departmentId
     * @param string $semester
     * @param string $academicYear
     * @param int|null $courseId
     * @return array
     */
    public function calculateEligibilityForDepartment(
        int $departmentId,
        string $semester,
        string $academicYear,
        ?int $courseId = null
    ): array {
        return $this->validateEligibility($departmentId, 75.0, $semester, $academicYear);
    }

    /**
     * Export eligibility data
     *
     * @param int $departmentId
     * @param array $filters
     * @return array
     */
    public function exportEligibilityData(int $departmentId, array $filters = []): array
    {
        $semester = $filters['semester'] ?? $this->getCurrentSemester();
        $academicYear = $filters['academic_year'] ?? $this->getCurrentAcademicYear();

        $eligibilities = $this->getEligibilityStatus($departmentId, $semester, $academicYear);

        return [
            'semester' => $semester,
            'academic_year' => $academicYear,
            'exported_at' => now(),
            'total_students' => $eligibilities->count(),
            'data' => $eligibilities->map(function ($eligibility) {
                return [
                    'matric_number' => $eligibility->student->matric_number,
                    'full_name' => $eligibility->student->full_name,
                    'attendance_percentage' => round($eligibility->attendance_percentage, 2),
                    'is_eligible' => in_array($eligibility->status, ['eligible', 'overridden']),
                    'override_reason' => $eligibility->override_reason,
                    'validated_at' => $eligibility->validated_at,
                ];
            })->toArray(),
        ];
    }
}