<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Classroom;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\AttendanceSession;

class CheckDatabase extends Command
{
    protected $signature = 'check:database';
    protected $description = 'Check database state and diagnose issues';

    public function handle()
    {
        $this->info('=== DATABASE DIAGNOSTIC CHECK ===');
        
        // Check Classes
        $this->checkClasses();
        
        // Check Lecturers
        $this->checkLecturers();
        
        // Check Students
        $this->checkStudents();
        
        // Check Attendance Sessions
        $this->checkAttendanceSessions();
        
        // Check Attendance Page Logic
        $this->checkAttendancePageLogic();
        
        $this->info('=== CHECK COMPLETE ===');
    }
    
    private function checkClasses()
    {
        $this->info("\n📚 CLASSES:");
        $classes = Classroom::with(['students', 'lecturer'])->get();
        
        if ($classes->isEmpty()) {
            $this->error('❌ No classes found in database!');
            return;
        }
        
        $this->info("✅ Found {$classes->count()} classes:");
        
        foreach ($classes as $class) {
            $lecturerName = $class->lecturer ? $class->lecturer->name : 'None';
            $studentCount = $class->students->count();
            $status = $class->is_active ? '✅ Active' : '❌ Inactive';
            
            $this->line("  • {$class->course_code} - {$class->class_name}");
            $this->line("    Lecturer: {$lecturerName}");
            $this->line("    Students: {$studentCount}");
            $this->line("    Level: {$class->level}");
            $this->line("    Status: {$status}");
            $this->line("");
        }
    }
    
    private function checkLecturers()
    {
        $this->info("\n👨‍🏫 LECTURERS:");
        $lecturers = Lecturer::with('classrooms')->get();
        
        if ($lecturers->isEmpty()) {
            $this->error('❌ No lecturers found in database!');
            return;
        }
        
        $this->info("✅ Found {$lecturers->count()} lecturers:");
        
        foreach ($lecturers as $lecturer) {
            $classCount = $lecturer->classrooms->count();
            $this->line("  • {$lecturer->name} (ID: {$lecturer->id})");
            $this->line("    Classes: {$classCount}");
            $this->line("");
        }
    }
    
    private function checkStudents()
    {
        $this->info("\n👨‍🎓 STUDENTS:");
        $students = Student::all();
        
        if ($students->isEmpty()) {
            $this->error('❌ No students found in database!');
            return;
        }
        
        $this->info("✅ Found {$students->count()} total students:");
        
        $csStudents = $students->where('department', 'Computer Science');
        $this->info("  • Computer Science: {$csStudents->count()}");
        
        $studentsByLevel = $students->groupBy('level');
        foreach ($studentsByLevel as $level => $levelStudents) {
            $csLevelStudents = $levelStudents->where('department', 'Computer Science');
            $this->line("    Level {$level}: {$csLevelStudents->count()} CS students");
        }
        
        $this->line("");
    }
    
    private function checkAttendanceSessions()
    {
        $this->info("\n📊 ATTENDANCE SESSIONS:");
        $sessions = AttendanceSession::with(['classroom', 'lecturer'])->get();
        
        if ($sessions->isEmpty()) {
            $this->warn('⚠️  No attendance sessions found');
        } else {
            $this->info("✅ Found {$sessions->count()} attendance sessions:");
            
            foreach ($sessions as $session) {
                $status = $session->is_active ? '🟢 Active' : '🔴 Ended';
                $this->line("  • Session ID: {$session->id}");
                $this->line("    Class: {$session->classroom->course_code}");
                $this->line("    Lecturer: {$session->lecturer->name}");
                $this->line("    Status: {$status}");
                $this->line("    Started: {$session->created_at}");
                $this->line("");
            }
        }
    }
    
    private function checkAttendancePageLogic()
    {
        $this->info("\n🔍 ATTENDANCE PAGE LOGIC CHECK:");
        
        // Get first lecturer (assuming this is the logged-in user)
        $lecturer = Lecturer::first();
        
        if (!$lecturer) {
            $this->error('❌ No lecturer found for attendance page check!');
            return;
        }
        
        $this->info("Checking for lecturer: {$lecturer->name} (ID: {$lecturer->id})");
        
        // Get classes assigned to this lecturer
        $classes = $lecturer->classrooms()->with(['students', 'attendanceSessions'])->get();
        
        if ($classes->isEmpty()) {
            $this->error('❌ No classes assigned to lecturer!');
            $this->info('This is why classes don\'t appear on attendance page.');
            return;
        }
        
        $this->info("✅ Found {$classes->count()} classes assigned to lecturer:");
        
        foreach ($classes as $class) {
            $studentCount = $class->students->count();
            $activeSession = $class->attendanceSessions()->where('is_active', true)->first();
            $sessionStatus = $activeSession ? '🟢 Active Session' : '⚪ No Active Session';
            
            $this->line("  • {$class->course_code} - {$class->class_name}");
            $this->line("    Students: {$studentCount}");
            $this->line("    Level: {$class->level}");
            $this->line("    Session: {$sessionStatus}");
            $this->line("");
        }
        
        // Check if students are properly assigned
        $totalStudents = $classes->flatMap->students->unique('id')->count();
        $this->info("Total unique students across all classes: {$totalStudents}");
        
        if ($totalStudents === 0) {
            $this->error('❌ No students assigned to lecturer\'s classes!');
            $this->info('This could cause issues in attendance sessions.');
        }
    }
} 