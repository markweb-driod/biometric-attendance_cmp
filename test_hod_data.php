<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== HOD Portal Database Status ===\n";

// Check semesters
$semesters = App\Models\Semester::all();
echo "Semesters: " . $semesters->count() . "\n";
foreach ($semesters as $semester) {
    echo "  - {$semester->name} {$semester->academic_year} (current: " . ($semester->is_current ? 'yes' : 'no') . ")\n";
}

// Check current semester
$currentSemester = App\Models\Semester::where('is_current', true)->first();
echo "\nCurrent Semester: " . ($currentSemester ? $currentSemester->name . ' ' . $currentSemester->academic_year : 'None') . "\n";

// Check HODs
$hods = App\Models\Hod::with(['user', 'department'])->get();
echo "\nHODs: " . $hods->count() . "\n";
foreach ($hods as $hod) {
    echo "  - {$hod->user->full_name} ({$hod->staff_id}) - {$hod->department->name}\n";
}

// Check courses with semesters
$coursesWithSemester = App\Models\Course::whereNotNull('semester_id')->count();
$totalCourses = App\Models\Course::count();
echo "\nCourses: {$totalCourses} total, {$coursesWithSemester} with semester assigned\n";

// Check students
$students = App\Models\Student::count();
echo "Students: {$students}\n";

// Check lecturers
$lecturers = App\Models\Lecturer::count();
echo "Lecturers: {$lecturers}\n";

// Check attendance sessions
$sessions = App\Models\AttendanceSession::count();
echo "Attendance Sessions: {$sessions}\n";

echo "\n=== End Status ===\n";