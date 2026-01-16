<?php

namespace App\Services;

use App\Models\ExamEligibility;
use App\Models\Student;
use Illuminate\Support\Collection;

class EligibilityResponseService
{
    /**
     * Format eligibility response (simple boolean by default)
     */
    public function formatResponse(
        Student $student,
        $eligibilityData,
        ?string $semester = null,
        ?string $academicYear = null,
        bool $detailed = false
    ): array {
        $baseResponse = [
            'eligible' => $this->isOverallEligible($eligibilityData),
            'student' => $student->matric_number,
            'semester' => $semester ?? 'current',
            'academic_year' => $academicYear ?? 'current',
        ];

        if ($detailed) {
            $baseResponse['details'] = $this->formatDetailedResponse($eligibilityData, $student);
        }

        return $baseResponse;
    }

    /**
     * Format single course eligibility
     */
    public function formatCourseResponse(
        ExamEligibility $eligibility,
        bool $detailed = false
    ): array {
        $response = [
            'eligible' => $eligibility->isEligible(),
            'student' => $eligibility->student->matric_number,
            'course_id' => $eligibility->course_id,
            'course_code' => $eligibility->course->course_code ?? null,
            'course_name' => $eligibility->course->course_name ?? null,
            'semester' => $eligibility->semester,
            'academic_year' => $eligibility->academic_year,
        ];

        if ($detailed) {
            $response['details'] = [
                'attendance_percentage' => round($eligibility->attendance_percentage, 2),
                'required_threshold' => round($eligibility->required_threshold, 2),
                'status' => $eligibility->status,
                'is_overridden' => $eligibility->isOverridden(),
                'override_reason' => $eligibility->override_reason,
                'validated_at' => $eligibility->validated_at?->toIso8601String(),
                'overridden_at' => $eligibility->overridden_at?->toIso8601String(),
            ];
        }

        return $response;
    }

    /**
     * Format all courses for a student
     */
    public function formatAllCoursesResponse(
        Collection $eligibilities,
        Student $student,
        ?string $semester = null,
        ?string $academicYear = null,
        bool $detailed = false
    ): array {
        $courses = $eligibilities->map(function ($eligibility) use ($detailed) {
            if ($detailed) {
                return [
                    'course_id' => $eligibility->course_id,
                    'course_code' => $eligibility->course->course_code ?? null,
                    'course_name' => $eligibility->course->course_name ?? null,
                    'eligible' => $eligibility->isEligible(),
                    'attendance_percentage' => round($eligibility->attendance_percentage, 2),
                    'required_threshold' => round($eligibility->required_threshold, 2),
                    'status' => $eligibility->status,
                ];
            }
            
            return [
                'course_id' => $eligibility->course_id,
                'course_code' => $eligibility->course->course_code ?? null,
                'course_name' => $eligibility->course->course_name ?? null,
                'eligible' => $eligibility->isEligible(),
            ];
        });

        $response = [
            'eligible' => $this->isOverallEligible($eligibilities),
            'student' => $student->matric_number,
            'semester' => $semester ?? 'current',
            'academic_year' => $academicYear ?? 'current',
            'courses' => $courses->toArray(),
            'total_courses' => $eligibilities->count(),
            'eligible_courses' => $eligibilities->filter(fn($e) => $e->isEligible())->count(),
        ];

        return $response;
    }

    /**
     * Format bulk response
     */
    public function formatBulkResponse(array $results, bool $detailed = false): array
    {
        return [
            'total' => count($results),
            'processed' => count(array_filter($results, fn($r) => !isset($r['error']))),
            'results' => $results,
        ];
    }

    /**
     * Check if student is overall eligible (ALL courses must be eligible)
     */
    protected function isOverallEligible($eligibilityData): bool
    {
        if ($eligibilityData instanceof Collection) {
            if ($eligibilityData->isEmpty()) {
                return false;
            }
            return $eligibilityData->every(fn($e) => $e->isEligible());
        }

        if (is_array($eligibilityData)) {
            if (empty($eligibilityData)) {
                return false;
            }
            return !in_array(false, array_map(fn($e) => $e->isEligible(), $eligibilityData));
        }

        if ($eligibilityData instanceof ExamEligibility) {
            return $eligibilityData->isEligible();
        }

        return false;
    }

    /**
     * Format detailed response
     */
    protected function formatDetailedResponse($eligibilityData, Student $student): array
    {
        if ($eligibilityData instanceof Collection) {
            return [
                'student_name' => $student->user->full_name ?? null,
                'department' => $student->department->name ?? null,
                'academic_level' => $student->academicLevel->name ?? null,
                'courses' => $eligibilityData->map(function ($eligibility) {
                    return [
                        'course_id' => $eligibility->course_id,
                        'course_code' => $eligibility->course->course_code ?? null,
                        'course_name' => $eligibility->course->course_name ?? null,
                        'eligible' => $eligibility->isEligible(),
                        'attendance_percentage' => round($eligibility->attendance_percentage, 2),
                        'required_threshold' => round($eligibility->required_threshold, 2),
                        'status' => $eligibility->status,
                        'is_overridden' => $eligibility->isOverridden(),
                        'override_reason' => $eligibility->override_reason,
                        'validated_at' => $eligibility->validated_at?->toIso8601String(),
                    ];
                })->toArray(),
            ];
        }

        if ($eligibilityData instanceof ExamEligibility) {
            return [
                'student_name' => $student->user->full_name ?? null,
                'department' => $student->department->name ?? null,
                'academic_level' => $student->academicLevel->name ?? null,
                'attendance_percentage' => round($eligibilityData->attendance_percentage, 2),
                'required_threshold' => round($eligibilityData->required_threshold, 2),
                'status' => $eligibilityData->status,
                'is_overridden' => $eligibilityData->isOverridden(),
                'override_reason' => $eligibilityData->override_reason,
                'validated_at' => $eligibilityData->validated_at?->toIso8601String(),
            ];
        }

        return [];
    }
}

