<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ExamEligibility;
use App\Models\Course;
use App\Services\ExamEligibilityService;
use App\Services\EligibilityResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EligibilityApiController extends Controller
{
    protected $eligibilityService;
    protected $responseService;

    public function __construct(
        ExamEligibilityService $eligibilityService,
        EligibilityResponseService $responseService
    ) {
        $this->eligibilityService = $eligibilityService;
        $this->responseService = $responseService;
    }

    /**
     * Check eligibility for a single student (current semester/academic year)
     * GET /api/v1/eligibility/student/{matricNumber}
     */
    public function checkStudent(Request $request, string $matricNumber)
    {
        $detailed = $request->boolean('detailed', false);

        try {
            $student = Student::with(['user', 'department', 'academicLevel'])
                ->where('matric_number', $matricNumber)
                ->where('is_active', true)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'error' => 'student_not_found',
                    'message' => 'Student not found or inactive.',
                ], 404);
            }

            $semester = $this->eligibilityService->getCurrentSemester();
            $academicYear = $this->eligibilityService->getCurrentAcademicYear();

            $cacheKey = "eligibility:student:{$student->id}:{$semester}:{$academicYear}:{$detailed}";
            
            return Cache::remember($cacheKey, config('api.cache.eligibility_ttl', 300), function () use ($student, $semester, $academicYear, $detailed) {
                $eligibilities = ExamEligibility::where('student_id', $student->id)
                    ->where('semester', $semester)
                    ->where('academic_year', $academicYear)
                    ->with(['course'])
                    ->get();

                if ($eligibilities->isEmpty()) {
                    return response()->json([
                        'eligible' => false,
                        'student' => $student->matric_number,
                        'semester' => $semester,
                        'academic_year' => $academicYear,
                        'message' => 'No eligibility records found for this semester.',
                    ], 200);
                }

                $response = $this->responseService->formatResponse(
                    $student,
                    $eligibilities,
                    $semester,
                    $academicYear,
                    $detailed
                );

                return response()->json(array_merge(['success' => true], $response), 200);
            });

        } catch (\Exception $e) {
            Log::error('Eligibility API Error', [
                'matric_number' => $matricNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    /**
     * Check eligibility for a specific course
     * GET /api/v1/eligibility/student/{matricNumber}/course/{courseId}
     */
    public function checkStudentCourse(Request $request, string $matricNumber, int $courseId)
    {
        $detailed = $request->boolean('detailed', false);
        $semester = $request->get('semester');
        $academicYear = $request->get('academic_year');

        try {
            $student = Student::where('matric_number', $matricNumber)
                ->where('is_active', true)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'error' => 'student_not_found',
                    'message' => 'Student not found or inactive.',
                ], 404);
            }

            $course = Course::find($courseId);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'error' => 'course_not_found',
                    'message' => 'Course not found.',
                ], 404);
            }

            $semester = $semester ?? $this->eligibilityService->getCurrentSemester();
            $academicYear = $academicYear ?? $this->eligibilityService->getCurrentAcademicYear();

            $cacheKey = "eligibility:student:{$student->id}:course:{$courseId}:{$semester}:{$academicYear}:{$detailed}";

            return Cache::remember($cacheKey, config('api.cache.eligibility_ttl', 300), function () use ($student, $courseId, $semester, $academicYear, $detailed) {
                $eligibility = ExamEligibility::where('student_id', $student->id)
                    ->where('course_id', $courseId)
                    ->where('semester', $semester)
                    ->where('academic_year', $academicYear)
                    ->with(['course', 'student'])
                    ->first();

                if (!$eligibility) {
                    return response()->json([
                        'success' => false,
                        'error' => 'eligibility_not_found',
                        'message' => 'No eligibility record found for this student and course.',
                    ], 404);
                }

                $response = $this->responseService->formatCourseResponse($eligibility, $detailed);

                return response()->json(array_merge(['success' => true], $response), 200);
            });

        } catch (\Exception $e) {
            Log::error('Eligibility API Error', [
                'matric_number' => $matricNumber,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    /**
     * Get eligibility for all courses for a student
     * GET /api/v1/eligibility/student/{matricNumber}/courses
     */
    public function checkStudentCourses(Request $request, string $matricNumber)
    {
        $detailed = $request->boolean('detailed', false);
        $semester = $request->get('semester');
        $academicYear = $request->get('academic_year');

        try {
            $student = Student::with(['user', 'department', 'academicLevel'])
                ->where('matric_number', $matricNumber)
                ->where('is_active', true)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'error' => 'student_not_found',
                    'message' => 'Student not found or inactive.',
                ], 404);
            }

            $semester = $semester ?? $this->eligibilityService->getCurrentSemester();
            $academicYear = $academicYear ?? $this->eligibilityService->getCurrentAcademicYear();

            $cacheKey = "eligibility:student:{$student->id}:courses:{$semester}:{$academicYear}:{$detailed}";

            return Cache::remember($cacheKey, config('api.cache.eligibility_ttl', 300), function () use ($student, $semester, $academicYear, $detailed) {
                $eligibilities = ExamEligibility::where('student_id', $student->id)
                    ->where('semester', $semester)
                    ->where('academic_year', $academicYear)
                    ->with(['course'])
                    ->get();

                if ($eligibilities->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'eligibility_not_found',
                        'message' => 'No eligibility records found for this semester.',
                    ], 404);
                }

                $response = $this->responseService->formatAllCoursesResponse(
                    $eligibilities,
                    $student,
                    $semester,
                    $academicYear,
                    $detailed
                );

                return response()->json(array_merge(['success' => true], $response), 200);
            });

        } catch (\Exception $e) {
            Log::error('Eligibility API Error', [
                'matric_number' => $matricNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    /**
     * Bulk check eligibility for multiple students
     * POST /api/v1/eligibility/bulk
     */
    public function checkBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'matric_numbers' => 'required|array|min:1|max:100',
            'matric_numbers.*' => 'required|string',
            'semester' => 'nullable|string',
            'academic_year' => 'nullable|string',
            'detailed' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'validation_error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $matricNumbers = $request->input('matric_numbers');
        $detailed = $request->boolean('detailed', false);
        $semester = $request->get('semester') ?? $this->eligibilityService->getCurrentSemester();
        $academicYear = $request->get('academic_year') ?? $this->eligibilityService->getCurrentAcademicYear();

        try {
            $results = [];

            foreach ($matricNumbers as $matricNumber) {
                try {
                    $student = Student::where('matric_number', $matricNumber)
                        ->where('is_active', true)
                        ->first();

                    if (!$student) {
                        $results[] = [
                            'matric_number' => $matricNumber,
                            'eligible' => false,
                            'error' => 'student_not_found',
                            'message' => 'Student not found or inactive.',
                        ];
                        continue;
                    }

                    $eligibilities = ExamEligibility::where('student_id', $student->id)
                        ->where('semester', $semester)
                        ->where('academic_year', $academicYear)
                        ->with(['course'])
                        ->get();

                    if ($eligibilities->isEmpty()) {
                        $results[] = [
                            'matric_number' => $matricNumber,
                            'eligible' => false,
                            'error' => 'eligibility_not_found',
                            'message' => 'No eligibility records found.',
                        ];
                        continue;
                    }

                    $response = $this->responseService->formatResponse(
                        $student,
                        $eligibilities,
                        $semester,
                        $academicYear,
                        $detailed
                    );

                    $results[] = $response;

                } catch (\Exception $e) {
                    Log::error('Bulk eligibility check error', [
                        'matric_number' => $matricNumber,
                        'error' => $e->getMessage(),
                    ]);

                    $results[] = [
                        'matric_number' => $matricNumber,
                        'eligible' => false,
                        'error' => 'processing_error',
                        'message' => 'Error processing this student.',
                    ];
                }
            }

            $bulkResponse = $this->responseService->formatBulkResponse($results, $detailed);

            return response()->json([
                'success' => true,
                'semester' => $semester,
                'academic_year' => $academicYear,
            ] + $bulkResponse, 200);

        } catch (\Exception $e) {
            Log::error('Bulk eligibility API Error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    /**
     * Check eligibility with specific semester and academic year
     * GET /api/v1/eligibility/student/{matricNumber}/semester/{semester}/academic-year/{academicYear}
     */
    public function checkWithSemester(Request $request, string $matricNumber, string $semester, string $academicYear)
    {
        $detailed = $request->boolean('detailed', false);

        try {
            $student = Student::with(['user', 'department', 'academicLevel'])
                ->where('matric_number', $matricNumber)
                ->where('is_active', true)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'error' => 'student_not_found',
                    'message' => 'Student not found or inactive.',
                ], 404);
            }

            $cacheKey = "eligibility:student:{$student->id}:{$semester}:{$academicYear}:{$detailed}";

            return Cache::remember($cacheKey, config('api.cache.eligibility_ttl', 300), function () use ($student, $semester, $academicYear, $detailed) {
                $eligibilities = ExamEligibility::where('student_id', $student->id)
                    ->where('semester', $semester)
                    ->where('academic_year', $academicYear)
                    ->with(['course'])
                    ->get();

                if ($eligibilities->isEmpty()) {
                    return response()->json([
                        'eligible' => false,
                        'student' => $student->matric_number,
                        'semester' => $semester,
                        'academic_year' => $academicYear,
                        'message' => 'No eligibility records found for this semester.',
                    ], 200);
                }

                $response = $this->responseService->formatResponse(
                    $student,
                    $eligibilities,
                    $semester,
                    $academicYear,
                    $detailed
                );

                return response()->json(array_merge(['success' => true], $response), 200);
            });

        } catch (\Exception $e) {
            Log::error('Eligibility API Error', [
                'matric_number' => $matricNumber,
                'semester' => $semester,
                'academic_year' => $academicYear,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    /**
     * Health check endpoint
     * GET /api/v1/eligibility/health
     */
    public function healthCheck()
    {
        try {
            $currentSemester = $this->eligibilityService->getCurrentSemester();
            $currentAcademicYear = $this->eligibilityService->getCurrentAcademicYear();

            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toIso8601String(),
                'service' => 'eligibility-api',
                'version' => '1.0.0',
                'current_semester' => $currentSemester,
                'current_academic_year' => $currentAcademicYear,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'timestamp' => now()->toIso8601String(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
