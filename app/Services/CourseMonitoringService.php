<?php

namespace App\Services;

use App\Models\Hod;
use App\Models\Classroom;
use App\Models\Lecturer;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CourseMonitoringService
{
    protected $hod;
    protected $cachePrefix = 'course_monitoring_';

    public function __construct(Hod $hod)
    {
        $this->hod = $hod;
    }

    /**
     * Get course and lecturer performance data for chart
     */
    public function getCoursePerformanceData($filters = [])
    {
        $cacheKey = $this->cachePrefix . 'performance_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $query = $this->buildBaseQuery();
            $query = $this->applyFilters($query, $filters);
            
            $data = $query->get();
            
            $formatted = $this->formatChartData($data, $filters);
            
            // Limit to top 10 courses to prevent overcrowding the chart
            return array_slice($formatted, 0, 10);
        });
    }

    /**
     * Get weekly attendance trends for courses
     */
    public function getWeeklyAttendanceTrends($filters = [])
    {
        $cacheKey = $this->cachePrefix . 'weekly_trends_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $weeks = $this->getSemesterWeeks($filters);
            $courses = $this->getCoursesForDepartment($filters);
            
            $trends = [];
            
            foreach ($courses as $course) {
                $courseData = [
                    'course_code' => $course->course_code ?? 'Unknown',
                    'course_name' => $course->course_name ?? 'Unknown',
                    'lecturer_name' => $course->lecturer_name ?? 'Unknown',
                    'weeks' => []
                ];
                
                foreach ($weeks as $week) {
                    $attendanceData = $this->getWeekAttendanceData($course->classroom_id ?? 0, $week, $filters);
                    $courseData['weeks'][] = [
                        'week' => $week,
                        'attendance_rate' => $attendanceData['attendance_rate'] ?? 0,
                        'total_sessions' => $attendanceData['total_sessions'] ?? 0,
                        'conducted_sessions' => $attendanceData['conducted_sessions'] ?? 0,
                        'punctuality_score' => $attendanceData['punctuality_score'] ?? 0
                    ];
                }
                
                $trends[] = $courseData;
            }
            
            return $trends;
        });
    }

    /**
     * Get lecturer performance metrics
     */
    public function getLecturerPerformanceMetrics($filters = [])
    {
        $cacheKey = $this->cachePrefix . 'lecturer_metrics_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $lecturers = $this->getLecturersForDepartment($filters);
            $metrics = [];
            
            foreach ($lecturers as $lecturer) {
                $performance = $this->calculateLecturerPerformance($lecturer->id, $filters);
                $metrics[] = [
                    'lecturer_id' => $lecturer->id,
                    'lecturer_name' => $lecturer->user->full_name,
                    'staff_id' => $lecturer->staff_id,
                    'department' => $lecturer->department->name,
                    'courses_count' => $performance['courses_count'],
                    'average_attendance_rate' => $performance['average_attendance_rate'],
                    'punctuality_score' => $performance['punctuality_score'],
                    'total_sessions' => $performance['total_sessions'],
                    'conducted_sessions' => $performance['conducted_sessions'],
                    'performance_rating' => $performance['performance_rating'],
                    'trend' => $performance['trend']
                ];
            }
            
            return $this->sortByPerformance($metrics, $filters);
        });
    }

    /**
     * Get top and low performing courses
     */
    public function getPerformanceAnalysis($filters = [])
    {
        $cacheKey = $this->cachePrefix . 'performance_analysis_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $courses = $this->getCoursePerformanceData($filters);
            
            $analysis = [
                'top_performers' => [],
                'low_performers' => [],
                'average_performance' => 0,
                'total_courses' => count($courses),
                'performance_distribution' => [
                    'excellent' => 0, // >90%
                    'good' => 0,      // 80-90%
                    'average' => 0,   // 70-80%
                    'poor' => 0       // <70%
                ]
            ];
            
            $totalPerformance = 0;
            
            foreach ($courses as $course) {
                $avgAttendance = $course['average_attendance_rate'];
                $totalPerformance += $avgAttendance;
                
                // Categorize performance
                if ($avgAttendance >= 90) {
                    $analysis['performance_distribution']['excellent']++;
                } elseif ($avgAttendance >= 80) {
                    $analysis['performance_distribution']['good']++;
                } elseif ($avgAttendance >= 70) {
                    $analysis['performance_distribution']['average']++;
                } else {
                    $analysis['performance_distribution']['poor']++;
                }
                
                // Top and low performers
                if ($avgAttendance >= 85) {
                    $analysis['top_performers'][] = $course;
                } elseif ($avgAttendance < 70) {
                    $analysis['low_performers'][] = $course;
                }
            }
            
            $analysis['average_performance'] = count($courses) > 0 ? $totalPerformance / count($courses) : 0;
            
            return $analysis;
        });
    }

    /**
     * Build base query for course data
     */
    private function buildBaseQuery()
    {
        return DB::table('classrooms')
            ->join('courses', 'classrooms.course_id', '=', 'courses.id')
            ->join('course_department', 'courses.id', '=', 'course_department.course_id')
            ->join('departments', 'course_department.department_id', '=', 'departments.id')
            ->join('lecturers', 'classrooms.lecturer_id', '=', 'lecturers.id')
            ->join('users', 'lecturers.user_id', '=', 'users.id')
            ->where('classrooms.is_active', true)
            ->where('course_department.department_id', $this->hod->department_id ?? 0)
            ->where('lecturers.is_active', true)
            ->select([
                'classrooms.id as classroom_id',
                'courses.id as course_id',
                'courses.course_code',
                'courses.course_name',
                'lecturers.id as lecturer_id',
                'users.full_name as lecturer_name',
                'lecturers.staff_id',
                'departments.name as department_name'
            ]);
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, $filters)
    {
        if (isset($filters['academic_level'])) {
            $query->where('courses.academic_level', $filters['academic_level']);
        }
        
        if (isset($filters['semester'])) {
            $query->where('classrooms.semester', $filters['semester']);
        }
        
        if (isset($filters['academic_year'])) {
            $query->where('classrooms.academic_year', $filters['academic_year']);
        }
        
        if (isset($filters['course_type'])) {
            $query->where('courses.course_type', $filters['course_type']);
        }
        
        if (isset($filters['lecturer_status'])) {
            if ($filters['lecturer_status'] === 'active') {
                $query->where('lecturers.is_active', true);
            } elseif ($filters['lecturer_status'] === 'inactive') {
                $query->where('lecturers.is_active', false);
            }
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
            $performance = $this->calculateCoursePerformance($item->classroom_id, $filters);
            
            $formatted[] = [
                'classroom_id' => $item->classroom_id,
                'course_code' => $item->course_code,
                'course_name' => $item->course_name,
                'lecturer_name' => $item->lecturer_name,
                'staff_id' => $item->staff_id,
                'department_name' => $item->department_name,
                'average_attendance_rate' => $performance['attendance_rate'],
                'total_sessions' => $performance['total_sessions'],
                'conducted_sessions' => $performance['conducted_sessions'],
                'punctuality_score' => $performance['punctuality_score'],
                'performance_rating' => $this->getPerformanceRating($performance['attendance_rate'])
            ];
        }
        
        return $formatted;
    }

    /**
     * Calculate course performance metrics
     */
    private function calculateCoursePerformance($classroomId, $filters)
    {
        $sessions = AttendanceSession::where('classroom_id', $classroomId)
            ->when(isset($filters['semester']), function ($query) use ($filters) {
                return $query->where('semester', $filters['semester']);
            })
            ->when(isset($filters['academic_year']), function ($query) use ($filters) {
                return $query->where('academic_year', $filters['academic_year']);
            })
            ->get();
        
        $totalSessions = $sessions->count();
        $conductedSessions = $sessions->where('status', 'completed')->count();
        
        // Calculate attendance rate based on conducted sessions
        $attendanceRate = $totalSessions > 0 ? ($conductedSessions / $totalSessions) * 100 : 0;
        
        // Calculate punctuality score
        $punctualityScore = $this->calculatePunctualityScore($sessions);
        
        return [
            'attendance_rate' => round($attendanceRate, 2),
            'total_sessions' => $totalSessions,
            'conducted_sessions' => $conductedSessions,
            'punctuality_score' => round($punctualityScore, 2)
        ];
    }

    /**
     * Calculate lecturer performance
     */
    private function calculateLecturerPerformance($lecturerId, $filters)
    {
        $classrooms = Classroom::where('lecturer_id', $lecturerId)
            ->where('is_active', true)
            ->when(isset($filters['semester']), function ($query) use ($filters) {
                return $query->where('semester', $filters['semester']);
            })
            ->when(isset($filters['academic_year']), function ($query) use ($filters) {
                return $query->where('academic_year', $filters['academic_year']);
            })
            ->get();
        
        $totalSessions = 0;
        $conductedSessions = 0;
        $punctualityScores = [];
        
        foreach ($classrooms as $classroom) {
            $sessions = AttendanceSession::where('classroom_id', $classroom->id)->get();
            $totalSessions += $sessions->count();
            $conductedSessions += $sessions->where('status', 'completed')->count();
            $punctualityScores[] = $this->calculatePunctualityScore($sessions);
        }
        
        $averageAttendanceRate = $totalSessions > 0 ? ($conductedSessions / $totalSessions) * 100 : 0;
        $averagePunctualityScore = count($punctualityScores) > 0 ? array_sum($punctualityScores) / count($punctualityScores) : 0;
        
        return [
            'courses_count' => $classrooms->count(),
            'average_attendance_rate' => round($averageAttendanceRate, 2),
            'punctuality_score' => round($averagePunctualityScore, 2),
            'total_sessions' => $totalSessions,
            'conducted_sessions' => $conductedSessions,
            'performance_rating' => $this->getPerformanceRating($averageAttendanceRate),
            'trend' => $this->calculateTrend($lecturerId, $filters)
        ];
    }

    /**
     * Calculate punctuality score
     */
    private function calculatePunctualityScore($sessions)
    {
        if ($sessions->isEmpty()) {
            return 0;
        }
        
        $onTimeSessions = $sessions->filter(function ($session) {
            if (!$session->start_time) {
                return false;
            }
            // For now, consider sessions that are ended/completed as on time
            // In real implementation, you'd check against scheduled time vs actual start
            return true;
        })->count();
        
        return ($onTimeSessions / $sessions->count()) * 100;
    }

    /**
     * Get performance rating
     */
    private function getPerformanceRating($attendanceRate)
    {
        if ($attendanceRate >= 90) return 'Excellent';
        if ($attendanceRate >= 80) return 'Good';
        if ($attendanceRate >= 70) return 'Average';
        return 'Poor';
    }

    /**
     * Calculate performance trend
     */
    private function calculateTrend($lecturerId, $filters)
    {
        // This would calculate trend over time
        // For now, return a placeholder
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
        return $this->buildBaseQuery()
            ->when(isset($filters['academic_level']), function ($query) use ($filters) {
                return $query->where('courses.academic_level', $filters['academic_level']);
            })
            ->get();
    }

    /**
     * Get lecturers for department
     */
    private function getLecturersForDepartment($filters)
    {
        return Lecturer::with(['user', 'department'])
            ->where('department_id', $this->hod->department_id ?? 0)
            ->when(isset($filters['lecturer_status']), function ($query) use ($filters) {
                if ($filters['lecturer_status'] === 'active') {
                    return $query->where('is_active', true);
                } elseif ($filters['lecturer_status'] === 'inactive') {
                    return $query->where('is_active', false);
                }
            })
            ->get();
    }

    /**
     * Get week attendance data
     */
    private function getWeekAttendanceData($courseId, $week, $filters)
    {
        $startDate = $this->getWeekStartDate($week, $filters);
        $endDate = $this->getWeekEndDate($week, $filters);
        
        $sessions = AttendanceSession::where('classroom_id', $courseId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->get();
        
        $totalSessions = $sessions->count();
        $conductedSessions = $sessions->where('status', 'ended')->count() + $sessions->where('status', 'completed')->count();
        $attendanceRate = $totalSessions > 0 ? ($conductedSessions / $totalSessions) * 100 : 0;
        $punctualityScore = $this->calculatePunctualityScore($sessions);
        
        return [
            'attendance_rate' => round($attendanceRate, 2),
            'total_sessions' => $totalSessions,
            'conducted_sessions' => $conductedSessions,
            'punctuality_score' => round($punctualityScore, 2)
        ];
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











