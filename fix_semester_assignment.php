<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Fixing Semester Assignments ===\n";

// Get current semester
$currentSemester = App\Models\Semester::where('is_current', true)->first();

if (!$currentSemester) {
    echo "No current semester found. Creating one...\n";
    $currentSemester = App\Models\Semester::create([
        'name' => 'First Semester',
        'code' => 'SEM1',
        'academic_year' => '2025/2026',
        'start_date' => now()->startOfMonth(),
        'end_date' => now()->addMonths(4)->endOfMonth(),
        'is_active' => true,
        'is_current' => true,
        'description' => 'First Semester 2025/2026'
    ]);
}

echo "Current Semester: {$currentSemester->name} {$currentSemester->academic_year}\n";

// Update all courses to use current semester
$courses = App\Models\Course::whereNull('semester_id')->get();
echo "Updating {$courses->count()} courses to use current semester...\n";

foreach ($courses as $course) {
    $course->update(['semester_id' => $currentSemester->id]);
    echo "  - Updated: {$course->course_code} - {$course->course_name}\n";
}

// Update classrooms to use current semester
$classrooms = App\Models\Classroom::all();
echo "\nUpdating {$classrooms->count()} classrooms with semester info...\n";

foreach ($classrooms as $classroom) {
    $classroom->update([
        'semester_id' => $currentSemester->id,
        'academic_year' => $currentSemester->academic_year
    ]);
}

// Update attendance sessions
$sessions = App\Models\AttendanceSession::all();
echo "Updating {$sessions->count()} attendance sessions with semester info...\n";

foreach ($sessions as $session) {
    $session->update([
        'semester_id' => $currentSemester->id,
        'academic_year' => $currentSemester->academic_year
    ]);
}

// Update students with current semester
$students = App\Models\Student::whereNull('current_semester_id')->get();
echo "Updating {$students->count()} students with current semester...\n";

foreach ($students as $student) {
    $student->update(['current_semester_id' => $currentSemester->id]);
}

echo "\n=== Semester Assignment Complete ===\n";