<?php

namespace App\Services;

use App\Models\Hod;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StudentAttendanceService
{
    protected $hod;
    protected $cachePrefix = 'student_attendance_';

    public function __construct(Hod $hod)
    {
        $this->hod = $hod;
    }

    /**
     * Get student attendance data for chart
     */
    public function getStudentAttendanceData($filters = [])
    {
        $cacheKey = $this->cachePrefix . 'attendance_data_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $query = $this->buildBaseQuery();
            $query = $this->applyFilters($query, $filters);
            
            $data = $query->get();
            
            return $this->formatChartData($data, $filters);
        });
    }

    /**
     * Get weekly student attendance trends
     */
    public function getWeeklyStudentTrends($filters = [])
    {
        $cacheKey = $this->cachePrefix . 'weekly_trends_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $weeks = $this->getSemesterWeeks($filters);
            $courses = $this->getCoursesForDepartment($filters);
            
            $trends = [];
            
            foreach ($courses as $course) {
                $courseData = [
                    'course_code' => $course->course_code ?? '',
                    'course_name' => $course->course_name ?? '',
                    'enrolled_students' => $course->enrolled_students ?? 0,
                    'weeks' => []
                ];
                
                foreach ($weeks as $week) {
                    $attendanceData = $this->getWeekStudentAttendanceData($course->classroom_id, $week, $filters);
                    $courseData['weeks'][] = [
                        'week' => $week,
                        'attendance_percentage' => $attendanceData['attendance_percentage'],
                        'total_students' => $attendanceData['total_students'],
                        'present_students' => $attendanceData['present_students'],
                        'absent_students' => $attendanceData['absent_students'],
                        'late_students' => $attendanceData['late_students']
                    ];
                }
                
                $trends[] = $courseData;
            }
            
            return $trends;
        });
    }

    /**
     * Get student performance metrics
     */
    public function getStudentPerformanceMetrics($filters = [])
    {
        $cacheKey = $this->cachePrefix . 'student_metrics_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $students = $this->getStudentsForDepartment($filters);
            $metrics = [];
            
            // Handle both paginated and non-paginated results
            $studentCollection = $students instanceof \Illuminate\Pagination\LengthAwarePaginator ? $students->items() : $students;
            
            foreach ($studentCollection as $student) {
                $performance = $this->calculateStudentPerformance($student->id, $filters);
                $metrics[] = [
                    'student_id' => $student->id,
                    'student_name' => $student->user->full_name ?? 'Unknown',
                    'matric_number' => $student->matric_number ?? '',
                    'department' => is_object($student->department) ? $student->department->name : ($student->department ?? 'Unknown'),
                    'academic_level' => is_object($student->academicLevel) ? $student->academicLevel->name : ($student->academicLevel ?? 'Unknown'),
                    'courses_enrolled' => $performance['courses_enrolled'] ?? 0,
                    'average_attendance_rate' => $performance['average_attendance_rate'] ?? 0,
                    'total_attendance_sessions' => $performance['total_attendance_sessions'] ?? 0,
                    'attended_sessions' => $performance['attended_sessions'] ?? 0,
                    'performance_rating' => $performance['performance_rating'] ?? 'Unknown',
                    'risk_level' => $performance['risk_level'] ?? 'Unknown',
                    'trend' => $performance['trend'] ?? 'stable'
                ];
            }
            
            $sortedMetrics = $this->sortByPerformance($metrics, $filters);
            
            // If paginated, return with pagination info
            if ($students instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                return new \Illuminate\Pagination\LengthAwarePaginator(
                    $sortedMetrics,
                    $students->total(),
                    $students->perPage(),
                    $students->currentPage(),
                    [
                        'path' => $students->path(),
                        'pageName' => $students->getPageName(),
                    ]
                );
            }
            
            return $sortedMetrics;
        });
    }

    /**
     * Get attendance analysis
     */
    public function getAttendanceAnalysis($filters = [])
    {
        $cacheKey = $this->cachePrefix . 'attendance_analysis_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $students = $this->getStudentPerformanceMetrics($filters);
            
            $analysis = [
                'total_students' => count($students),
                'attendance_distribution' => [
                    'excellent' => 0, // >90%
                    'good' => 0,      // 80-90%
                    'average' => 0,   // 70-80%
                    'poor' => 0,      // 60-70%
                    'critical' => 0   // <60%
                ],
                'risk_analysis' => [
                    'high_risk' => 0,
                    'medium_risk' => 0,
                    'low_risk' => 0
                ],
                'average_attendance' => 0,
                'top_performers' => [],
                'at_risk_students' => []
            ];
            
            $totalAttendance = 0;
            
            foreach ($students as $student) {
                $attendanceRate = $student['average_attendance_rate'];
                $totalAttendance += $attendanceRate;
                
                // Categorize attendance
                if ($attendanceRate >= 90) {
                    $analysis['attendance_distribution']['excellent']++;
                } elseif ($attendanceRate >= 80) {
                    $analysis['attendance_distribution']['good']++;
                } elseif ($attendanceRate >= 70) {
                    $analysis['attendance_distribution']['average']++;
                } elseif ($attendanceRate >= 60) {
                    $analysis['attendance_distribution']['poor']++;
                } else {
                    $analysis['attendance_distribution']['critical']++;
                }
                
                // Risk analysis
                if ($attendanceRate < 60) {
                    $analysis['risk_analysis']['high_risk']++;
                    $analysis['at_risk_students'][] = $student;
                } elseif ($attendanceRate < 75) {
                    $analysis['risk_analysis']['medium_risk']++;
                } else {
                    $analysis['risk_analysis']['low_risk']++;
                }
                
                // Top performers
                if ($attendanceRate >= 90) {
                    $analysis['top_performers'][] = $student;
                }
            }
            
            $analysis['average_attendance'] = count($students) > 0 ? $totalAttendance / count($students) : 0;
            
            return $analysis;
        });
    }

    /**
     * Get course-wise attendance summary
     */
    public function getCourseAttendanceSummary($filters = [])
    {
        $cacheKey = $this->cachePrefix . 'course_summary_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $courses = $this->getCoursesForDepartment($filters);
            $summary = [];
            
            foreach ($courses as $course) {
                $attendanceData = $this->getCourseAttendanceData($course->classroom_id, $filters);
                $summary[] = [
                    'course_code' => $course->course_code ?? '',
                    'course_name' => $course->course_name ?? '',
                    'lecturer_name' => $course->lecturer_name ?? '',
                    'enrolled_students' => $attendanceData['enrolled_students'] ?? 0,
                    'average_attendance_rate' => $attendanceData['average_attendance_rate'] ?? 0,
                    'total_sessions' => $attendanceData['total_sessions'] ?? 0,
                    'attendance_trend' => $attendanceData['attendance_trend'] ?? 'stable',
                    'performance_rating' => $this->getPerformanceRating($attendanceData['average_attendance_rate'] ?? 0)
                ];
            }
            
            return $summary;
        });
    }

    /**
     * Get course attendance by time period for bar chart
     */
    public function getCourseAttendanceByTimePeriod($filters = [])
    {
        $timePeriod = $filters['time_period'] ?? 'all';
        $week = $filters['week'] ?? null;
        
        // Use shorter cache for live view
        $cacheDuration = $timePeriod === 'live' ? 60 : 300;
        $cacheKey = $this->cachePrefix . 'course_time_period_' . $timePeriod . '_' . ($week ?? 'all') . '_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $cacheDuration, function () use ($timePeriod, $week, $filters) {
            $courses = $this->getCoursesForDepartment($filters);
            $courseAttendance = [];
            
            foreach ($courses as $course) {
                $attendancePercentage = 0;
                
                // If week is specified, get attendance for that specific week
                if ($week) {
                    $attendancePercentage = $this->getCourseAttendanceByWeek($course->classroom_id, $week, $filters);
                } else {
                    switch ($timePeriod) {
                        case 'live':
                            $attendancePercentage = $this->getCourseAttendanceToday($course->classroom_id, $filters);
                            break;
                        case 'semester':
                            $attendancePercentage = $this->getCourseAttendanceBySemester($course->classroom_id, $filters);
                            break;
                        case 'all':
                        default:
                            $attendancePercentage = $this->getCourseAttendanceAllTime($course->classroom_id, $filters);
                            break;
                    }
                }
                
                $courseAttendance[] = [
                    'course_code' => $course->course_code ?? '',
                    'course_name' => $course->course_name ?? '',
                    'lecturer_name' => $course->lecturer_name ?? '',
                    'attendance_percentage' => $attendancePercentage
                ];
            }
            
            return $courseAttendance;
        });
    }

    /**
     * Get course attendance for a specific week
     */
    private function getCourseAttendanceByWeek($classroomId, $week, $filters)
    {
        $classroom = Classroom::with(['students'])->find($classroomId);
        if (!$classroom) {
            return 0;
        }
        
        $startDate = $this->getWeekStartDate($week, $filters);
        $endDate = $this->getWeekEndDate($week, $filters);
        
        // Get sessions for the specified week
        $sessions = AttendanceSession::where('classroom_id', $classroomId)
            ->whereBetween('scheduled_start_time', [$startDate, $endDate])
            ->get();
        
        if ($sessions->isEmpty()) {
            return 0;
        }
        
        $enrolledStudents = $classroom->students ? $classroom->students->count() : 0;
        $totalSessions = $sessions->count();
        
        if ($enrolledStudents == 0 || $totalSessions == 0) {
            return 0;
        }
        
        $totalPossibleAttendances = $enrolledStudents * $totalSessions;
        $actualAttendances = 0;
        
        foreach ($sessions as $session) {
            $actualAttendances += Attendance::where('attendance_session_id', $session->id)
                ->where('status', 'present')
                ->count();
        }
        
        return $totalPossibleAttendances > 0 ? round(($actualAttendances / $totalPossibleAttendances) * 100, 2) : 0;
    }

    /**
     * Get course attendance for all time
     */
    private function getCourseAttendanceAllTime($classroomId, $filters)
    {
        return $this->getCourseAttendanceData($classroomId, $filters)['average_attendance_rate'] ?? 0;
    }

    /**
     * Get course attendance by semester
     */
    private function getCourseAttendanceBySemester($classroomId, $filters)
    {
        $semester = $filters['semester'] ?? null;
        
        if (!$semester) {
            return $this->getCourseAttendanceAllTime($classroomId, $filters);
        }
        
        $classroom = Classroom::with(['students'])->find($classroomId);
        if (!$classroom) {
            return 0;
        }
        
        // Get sessions for the specified semester
        $sessions = AttendanceSession::where('classroom_id', $classroomId)
            ->when($semester, function ($query) use ($semester) {
                return $query->where('semester', $semester);
            })
            ->get();
        
        if ($sessions->isEmpty()) {
            return 0;
        }
        
        $enrolledStudents = $classroom->students ? $classroom->students->count() : 0;
        $totalSessions = $sessions->count();
        
        if ($enrolledStudents == 0 || $totalSessions == 0) {
            return 0;
        }
        
        $totalPossibleAttendances = $enrolledStudents * $totalSessions;
        $actualAttendances = 0;
        
        foreach ($sessions as $session) {
            $actualAttendances += Attendance::where('attendance_session_id', $session->id)
                ->where('status', 'present')
                ->count();
        }
        
        return $totalPossibleAttendances > 0 ? round(($actualAttendances / $totalPossibleAttendances) * 100, 2) : 0;
    }

    /**
     * Get course attendance for today (live view)
     */
    private function getCourseAttendanceToday($classroomId, $filters)
    {
        $classroom = Classroom::with(['students'])->find($classroomId);
        if (!$classroom) {
            return 0;
        }
        
        $today = now()->toDateString();
        
        // Get today's sessions
        $sessions = AttendanceSession::where('classroom_id', $classroomId)
            ->where(function ($query) use ($today) {
                $query->whereDate('scheduled_start_time', $today)
                      ->orWhereDate('created_at', $today);
            })
            ->get();
        
        if ($sessions->isEmpty()) {
            return 0;
        }
        
        $enrolledStudents = $classroom->students ? $classroom->students->count() : 0;
        
        if ($enrolledStudents == 0) {
            return 0;
        }
        
        $totalPossibleAttendances = $enrolledStudents * $sessions->count();
        $actualAttendances = 0;
        
        foreach ($sessions as $session) {
            $actualAttendances += Attendance::where('attendance_session_id', $session->id)
                ->where('status', 'present')
                ->count();
        }
        
        return $totalPossibleAttendances > 0 ? round(($actualAttendances / $totalPossibleAttendances) * 100, 2) : 0;
    }

    /**
     * Build base query for student data
     */
    private function buildBaseQuery()
    {
        return DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->join('departments', 'students.department_id', '=', 'departments.id')
            ->join('academic_levels', 'students.academic_level_id', '=', 'academic_levels.id')
            ->where('students.department_id', $this->hod->department_id ?? 0)
            ->where('students.is_active', true)
            ->select([
                'students.id as student_id',
                'students.matric_number',
                'users.full_name as student_name',
                'departments.name as department_name',
                'academic_levels.name as academic_level',
                'academic_levels.code as level_number'
            ]);
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, $filters)
    {
        if (isset($filters['academic_level'])) {
            $query->where('academic_levels.code', $filters['academic_level']);
        }
        
        if (isset($filters['semester'])) {
            // This would need to be joined with enrollments
        }
        
        if (isset($filters['academic_year'])) {
            // This would need to be joined with enrollments
        }
        
        if (isset($filters['attendance_threshold'])) {
            // This would need to be calculated
        }
        
        return $query;
    }

    /**
     * Format data for chart display
     */
    private function formatChartData($data, $filters)
    {
        $formatted = [];
        
        foreach ($data as $item) {
            $performance = $this->calculateStudentPerformance($item->student_id, $filters);
            
            $formatted[] = [
                'student_id' => $item->student_id,
                'matric_number' => $item->matric_number,
                'student_name' => $item->student_name,
                'department_name' => $item->department_name,
                'academic_level' => $item->academic_level,
                'average_attendance_rate' => $performance['average_attendance_rate'],
                'courses_enrolled' => $performance['courses_enrolled'],
                'total_attendance_sessions' => $performance['total_attendance_sessions'],
                'attended_sessions' => $performance['attended_sessions'],
                'performance_rating' => $performance['performance_rating'],
                'risk_level' => $performance['risk_level']
            ];
        }
        
        return $formatted;
    }

    /**
     * Calculate student performance metrics
     */
    private function calculateStudentPerformance($studentId, $filters)
    {
        // Get all courses the student is enrolled in
        $enrollments = DB::table('class_student')
            ->join('classrooms', 'class_student.classroom_id', '=', 'classrooms.id')
            ->where('class_student.student_id', $studentId)
            ->when(isset($filters['semester']), function ($query) use ($filters) {
                return $query->where('classrooms.semester', $filters['semester']);
            })
            ->when(isset($filters['academic_year']), function ($query) use ($filters) {
                return $query->where('classrooms.academic_year', $filters['academic_year']);
            })
            ->get();
        
        $totalSessions = 0;
        $attendedSessions = 0;
        $coursesEnrolled = $enrollments->count();
        
        foreach ($enrollments as $enrollment) {
            $sessions = AttendanceSession::where('classroom_id', $enrollment->classroom_id)->get();
            $totalSessions += $sessions->count();
            
            $attendances = Attendance::where('student_id', $studentId)
                ->whereIn('attendance_session_id', $sessions->pluck('id'))
                ->where('status', 'present')
                ->count();
            
            $attendedSessions += $attendances;
        }
        
        $averageAttendanceRate = $totalSessions > 0 ? ($attendedSessions / $totalSessions) * 100 : 0;
        
        return [
            'courses_enrolled' => $coursesEnrolled,
            'average_attendance_rate' => round($averageAttendanceRate, 2),
            'total_attendance_sessions' => $totalSessions,
            'attended_sessions' => $attendedSessions,
            'performance_rating' => $this->getPerformanceRating($averageAttendanceRate),
            'risk_level' => $this->getRiskLevel($averageAttendanceRate),
            'trend' => $this->calculateStudentTrend($studentId, $filters)
        ];
    }

    /**
     * Get course attendance data
     */
    private function getCourseAttendanceData($classroomId, $filters)
    {
        $classroom = Classroom::with(['students', 'course', 'lecturer.user'])->find($classroomId);
        $sessions = AttendanceSession::where('classroom_id', $classroomId)->get();
        
        $enrolledStudents = $classroom->students ? $classroom->students->count() : 0;
        $totalSessions = $sessions->count();
        
        $totalPossibleAttendances = $enrolledStudents * $totalSessions;
        $actualAttendances = 0;
        
        foreach ($sessions as $session) {
            $attendances = Attendance::where('attendance_session_id', $session->id)
                ->where('status', 'present')
                ->count();
            $actualAttendances += $attendances;
        }
        
        $averageAttendanceRate = $totalPossibleAttendances > 0 ? ($actualAttendances / $totalPossibleAttendances) * 100 : 0;
        
        return [
            'enrolled_students' => $enrolledStudents,
            'average_attendance_rate' => round($averageAttendanceRate, 2),
            'total_sessions' => $totalSessions,
            'attendance_trend' => $this->calculateCourseTrend($classroomId, $filters)
        ];
    }

    /**
     * Get week student attendance data
     */
    private function getWeekStudentAttendanceData($classroomId, $week, $filters)
    {
        $startDate = $this->getWeekStartDate($week, $filters);
        $endDate = $this->getWeekEndDate($week, $filters);
        
        $sessions = AttendanceSession::where('classroom_id', $classroomId)
            ->whereBetween('scheduled_start_time', [$startDate, $endDate])
            ->get();
        
        $classroom = Classroom::with('students')->find($classroomId);
        $totalStudents = $classroom->students ? $classroom->students->count() : 0;
        
        $presentStudents = 0;
        $lateStudents = 0;
        $absentStudents = 0;
        
        foreach ($sessions as $session) {
            $attendances = Attendance::where('attendance_session_id', $session->id)->get();
            $presentStudents += $attendances->where('status', 'present')->count();
            $lateStudents += $attendances->where('status', 'late')->count();
            $absentStudents += $totalStudents - $attendances->count();
        }
        
        $totalPossibleAttendances = $totalStudents * $sessions->count();
        $actualAttendances = $presentStudents + $lateStudents;
        $attendancePercentage = $totalPossibleAttendances > 0 ? ($actualAttendances / $totalPossibleAttendances) * 100 : 0;
        
        return [
            'attendance_percentage' => round($attendancePercentage, 2),
            'total_students' => $totalStudents,
            'present_students' => $presentStudents,
            'absent_students' => $absentStudents,
            'late_students' => $lateStudents
        ];
    }

    /**
     * Get performance rating
     */
    private function getPerformanceRating($attendanceRate)
    {
        if ($attendanceRate >= 90) return 'Excellent';
        if ($attendanceRate >= 80) return 'Good';
        if ($attendanceRate >= 70) return 'Average';
        if ($attendanceRate >= 60) return 'Poor';
        return 'Critical';
    }

    /**
     * Get risk level
     */
    private function getRiskLevel($attendanceRate)
    {
        if ($attendanceRate < 60) return 'High Risk';
        if ($attendanceRate < 75) return 'Medium Risk';
        return 'Low Risk';
    }

    /**
     * Calculate student trend
     */
    private function calculateStudentTrend($studentId, $filters)
    {
        // This would calculate trend over time
        return 'stable';
    }

    /**
     * Calculate course trend
     */
    private function calculateCourseTrend($classroomId, $filters)
    {
        // This would calculate trend over time
        return 'stable';
    }

    /**
     * Get semester weeks
     */
    private function getSemesterWeeks($filters)
    {
        return range(1, 14); // Standard 14-week semester
    }

    /**
     * Get courses for department
     */
    private function getCoursesForDepartment($filters)
    {
        return DB::table('classrooms')
            ->join('courses', 'classrooms.course_id', '=', 'courses.id')
            ->join('course_department', 'courses.id', '=', 'course_department.course_id')
            ->join('departments', 'course_department.department_id', '=', 'departments.id')
            ->join('lecturers', 'classrooms.lecturer_id', '=', 'lecturers.id')
            ->join('users', 'lecturers.user_id', '=', 'users.id')
            ->where('classrooms.is_active', true)
            ->where('course_department.department_id', $this->hod->department_id ?? 0)
            ->when(isset($filters['academic_level']), function ($query) use ($filters) {
                return $query->where('courses.academic_level_id', $filters['academic_level']);
            })
            ->select([
                'classrooms.id as classroom_id',
                'courses.course_code',
                'courses.course_name',
                'users.full_name as lecturer_name'
            ])
            ->get();
    }

    /**
     * Get students for department
     */
    private function getStudentsForDepartment($filters)
    {
        $query = Student::with(['user', 'department', 'academicLevel'])
            ->where('department_id', $this->hod->department_id ?? 0)
            ->where('is_active', true)
            ->when(isset($filters['academic_level']), function ($query) use ($filters) {
                return $query->where('academic_level_id', $filters['academic_level']);
            })
            ->when(isset($filters['search']) && !empty($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                return $query->where(function ($q) use ($search) {
                    $q->where('matric_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('full_name', 'like', "%{$search}%")
                                   ->orWhere('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            });

        // Apply sorting
        if (isset($filters['sort_by']) && isset($filters['sort_order'])) {
            $sortBy = $filters['sort_by'];
            $sortOrder = $filters['sort_order'];
            
            if ($sortBy === 'student_name') {
                $query->join('users', 'students.user_id', '=', 'users.id')
                      ->orderBy('users.full_name', $sortOrder)
                      ->select('students.*');
            } elseif ($sortBy === 'matric_number') {
                $query->orderBy('matric_number', $sortOrder);
            } elseif ($sortBy === 'academic_level') {
                $query->join('academic_levels', 'students.academic_level_id', '=', 'academic_levels.id')
                      ->orderBy('academic_levels.name', $sortOrder)
                      ->select('students.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        // Apply pagination if requested
        if (isset($filters['per_page']) && isset($filters['page'])) {
            $perPage = (int) $filters['per_page'];
            $page = (int) $filters['page'];
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        return $query->get();
    }

    /**
     * Get week start date
     */
    private function getWeekStartDate($week, $filters)
    {
        $semesterStart = $filters['semester_start'] ?? now()->startOfMonth();
        return \Carbon\Carbon::parse($semesterStart)->addWeeks($week - 1);
    }

    /**
     * Get week end date
     */
    private function getWeekEndDate($week, $filters)
    {
        $semesterStart = $filters['semester_start'] ?? now()->startOfMonth();
        return \Carbon\Carbon::parse($semesterStart)->addWeeks($week - 1)->endOfWeek();
    }

    /**
     * Sort by performance
     */
    private function sortByPerformance($metrics, $filters)
    {
        $sortBy = $filters['sort_by'] ?? 'average_attendance_rate';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        
        usort($metrics, function ($a, $b) use ($sortBy, $sortOrder) {
            if ($sortOrder === 'asc') {
                return $a[$sortBy] <=> $b[$sortBy];
            } else {
                return $b[$sortBy] <=> $a[$sortBy];
            }
        });
        
        return $metrics;
    }

    /**
     * Clear cache for HOD
     */
    public function clearCache()
    {
        $pattern = $this->cachePrefix . '*';
        Cache::forget($pattern);
    }
}











