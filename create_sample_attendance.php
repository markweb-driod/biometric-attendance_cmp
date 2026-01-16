<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Creating Sample Attendance Data ===\n";

// Get current semester
$currentSemester = App\Models\Semester::where('is_current', true)->first();

// Get some classrooms
$classrooms = App\Models\Classroom::with(['students', 'course', 'lecturer'])->take(3)->get();

echo "Creating attendance sessions and records...\n";

foreach ($classrooms as $classroom) {
    echo "\nProcessing classroom: {$classroom->course->course_name}\n";
    
    // Create 5 attendance sessions for this classroom
    for ($i = 1; $i <= 5; $i++) {
        $session = App\Models\AttendanceSession::create([
            'classroom_id' => $classroom->id,
            'lecturer_id' => $classroom->lecturer_id,
            'session_name' => "Week {$i} - {$classroom->course->course_name}",
            'start_time' => now()->subDays(7 * $i)->setTime(9, 0),
            'end_time' => now()->subDays(7 * $i)->setTime(11, 0),
            'status' => 'ended',
            'is_active' => false,
            'notes' => "Attendance session for week {$i}",
            'code' => 'ATT' . str_pad(($classroom->id * 10 + $i), 4, '0', STR_PAD_LEFT)
        ]);
        
        echo "  Created session: {$session->session_name}\n";
        
        // Create attendance records for students in this classroom
        $students = $classroom->students;
        $attendanceCount = 0;
        
        foreach ($students as $student) {
            // Simulate different attendance patterns
            $attendanceRate = rand(60, 95); // Random attendance rate between 60-95%
            $shouldAttend = rand(1, 100) <= $attendanceRate;
            
            if ($shouldAttend) {
                App\Models\Attendance::create([
                    'student_id' => $student->id,
                    'attendance_session_id' => $session->id,
                    'status' => rand(1, 10) <= 8 ? 'present' : 'late' // 80% present, 20% late
                ]);
                $attendanceCount++;
            }
        }
        
        echo "    - {$attendanceCount}/{$students->count()} students attended\n";
    }
}

// Create some exam eligibility records
echo "\nCreating exam eligibility records...\n";

$students = App\Models\Student::where('department_id', 1)->take(50)->get(); // Computer Science dept
$courses = App\Models\Course::whereHas('classrooms', function($q) {
    $q->whereHas('lecturer', function($query) {
        $query->where('department_id', 1);
    });
})->get();

foreach ($students as $student) {
    foreach ($courses->take(3) as $course) { // Each student in 3 courses
        // Calculate attendance percentage
        $totalSessions = App\Models\AttendanceSession::whereHas('classroom', function($q) use ($course) {
            $q->where('course_id', $course->id);
        })->count();
        
        $attendedSessions = App\Models\Attendance::where('student_id', $student->id)
            ->whereHas('session.classroom', function($q) use ($course) {
                $q->where('course_id', $course->id);
            })
            ->where('status', 'present')
            ->count();
        
        $attendancePercentage = $totalSessions > 0 ? ($attendedSessions / $totalSessions) * 100 : 0;
        $isEligible = $attendancePercentage >= 75;
        
        App\Models\ExamEligibility::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'semester' => $currentSemester->name,
            'academic_year' => $currentSemester->academic_year,
            'attendance_percentage' => round($attendancePercentage, 2),
            'required_threshold' => 75.0,
            'status' => $isEligible ? 'eligible' : 'ineligible',
            'validated_at' => now(),
            'validation_details' => [
                'total_sessions' => $totalSessions,
                'attended_sessions' => $attendedSessions,
                'calculated_at' => now()
            ]
        ]);
    }
}

echo "Created exam eligibility records for " . ($students->count() * 3) . " student-course combinations\n";

echo "\n=== Sample Data Creation Complete ===\n";