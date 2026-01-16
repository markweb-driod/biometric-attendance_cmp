<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing HOD Portal Functionality ===\n";

// Get the HOD
$hod = App\Models\Hod::with(['user', 'department'])->first();

if (!$hod) {
    echo "No HOD found in database!\n";
    exit;
}

echo "Testing HOD: {$hod->user->full_name} ({$hod->staff_id})\n";
echo "Department: {$hod->department->name}\n";

// Test HOD Dashboard Service
echo "\n--- Testing HOD Dashboard Service ---\n";
try {
    $dashboardService = new App\Services\StudentAttendanceService($hod);
    
    // Test getting department overview
    $students = App\Models\Student::where('department_id', $hod->department_id)->count();
    $lecturers = App\Models\Lecturer::where('department_id', $hod->department_id)->count();
    $courses = App\Models\Course::whereHas('classrooms', function($q) use ($hod) {
        $q->whereHas('lecturer', function($query) use ($hod) {
            $query->where('department_id', $hod->department_id);
        });
    })->count();
    
    echo "Department Stats:\n";
    echo "  - Students: {$students}\n";
    echo "  - Lecturers: {$lecturers}\n";
    echo "  - Courses: {$courses}\n";
    
} catch (Exception $e) {
    echo "Error testing dashboard service: " . $e->getMessage() . "\n";
}

// Test Course Monitoring Service
echo "\n--- Testing Course Monitoring Service ---\n";
try {
    $courseService = new App\Services\CourseMonitoringService($hod);
    $filters = ['semester' => 'First Semester', 'academic_year' => '2025/2026'];
    
    $courseData = $courseService->getCoursePerformanceData($filters);
    echo "Course Performance Data: " . count($courseData) . " courses found\n";
    
    if (count($courseData) > 0) {
        $firstCourse = $courseData[0];
        echo "  - Sample Course: {$firstCourse['course_name']} by {$firstCourse['lecturer_name']}\n";
        echo "  - Attendance Rate: {$firstCourse['average_attendance_rate']}%\n";
    }
    
} catch (Exception $e) {
    echo "Error testing course monitoring: " . $e->getMessage() . "\n";
}

// Test Student Attendance Service
echo "\n--- Testing Student Attendance Service ---\n";
try {
    $studentService = new App\Services\StudentAttendanceService($hod);
    $filters = ['academic_level' => '300'];
    
    $studentData = $studentService->getStudentAttendanceData($filters);
    echo "Student Attendance Data: " . count($studentData) . " students found\n";
    
    if (count($studentData) > 0) {
        $firstStudent = $studentData[0];
        echo "  - Sample Student: {$firstStudent['student_name']} ({$firstStudent['matric_number']})\n";
        echo "  - Attendance Rate: {$firstStudent['average_attendance_rate']}%\n";
    }
    
} catch (Exception $e) {
    echo "Error testing student attendance: " . $e->getMessage() . "\n";
}

// Test Exam Eligibility Service
echo "\n--- Testing Exam Eligibility Service ---\n";
try {
    $examService = new App\Services\ExamEligibilityService();
    $filters = ['semester' => 'First Semester', 'academic_year' => '2025/2026'];
    
    $eligibilityStats = $examService->getEligibilityStats($hod->department_id, $filters);
    echo "Exam Eligibility Stats:\n";
    echo "  - Total Students: {$eligibilityStats['total']}\n";
    echo "  - Eligible: {$eligibilityStats['eligible']}\n";
    echo "  - Ineligible: {$eligibilityStats['ineligible']}\n";
    echo "  - Average Attendance: {$eligibilityStats['average_attendance']}%\n";
    
} catch (Exception $e) {
    echo "Error testing exam eligibility: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";