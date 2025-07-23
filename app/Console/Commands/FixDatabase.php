<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Classroom;
use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class FixDatabase extends Command
{
    protected $signature = 'fix:database';
    protected $description = 'Fix common database issues';

    public function handle()
    {
        $this->info('=== FIXING DATABASE ISSUES ===');
        
        // Fix 1: Ensure classes have lecturers assigned
        $this->fixClassLecturerAssignments();
        
        // Fix 2: Ensure students are assigned to classes by level
        $this->fixStudentClassAssignments();
        
        // Fix 3: Ensure classes are active
        $this->fixClassStatus();
        
        // Fix 4: Clean up any orphaned records
        $this->cleanupOrphanedRecords();
        
        $this->info('=== FIXES COMPLETE ===');
        
        // Run check after fixes
        $this->call('check:database');
    }
    
    private function fixClassLecturerAssignments()
    {
        $this->info("\n🔧 Fixing class-lecturer assignments...");
        
        $lecturers = Lecturer::all();
        if ($lecturers->isEmpty()) {
            $this->error('❌ No lecturers found! Cannot assign classes.');
            return;
        }
        
        $unassignedClasses = Classroom::whereNull('lecturer_id')->get();
        
        if ($unassignedClasses->isEmpty()) {
            $this->info('✅ All classes already have lecturers assigned.');
            return;
        }
        
        $this->info("Found {$unassignedClasses->count()} classes without lecturers.");
        
        foreach ($unassignedClasses as $class) {
            // Assign to first lecturer (you can modify this logic)
            $lecturer = $lecturers->first();
            $class->update(['lecturer_id' => $lecturer->id]);
            $this->line("  • Assigned {$class->course_code} to {$lecturer->name}");
        }
        
        $this->info('✅ Class-lecturer assignments fixed.');
    }
    
    private function fixStudentClassAssignments()
    {
        $this->info("\n🔧 Fixing student-class assignments...");
        
        $classes = Classroom::all();
        $students = Student::where('department', 'Computer Science')->get();
        
        if ($classes->isEmpty() || $students->isEmpty()) {
            $this->error('❌ No classes or students found!');
            return;
        }
        
        // Clear existing assignments
        DB::table('class_student')->delete();
        
        $assignments = 0;
        
        foreach ($classes as $class) {
            // Get students of the same level as the class
            $matchingStudents = $students->where('level', $class->level);
            
            foreach ($matchingStudents as $student) {
                // Check if assignment already exists
                $exists = DB::table('class_student')
                    ->where('classroom_id', $class->id)
                    ->where('student_id', $student->id)
                    ->exists();
                
                if (!$exists) {
                    DB::table('class_student')->insert([
                        'classroom_id' => $class->id,
                        'student_id' => $student->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $assignments++;
                }
            }
        }
        
        $this->info("✅ Assigned {$assignments} student-class relationships.");
    }
    
    private function fixClassStatus()
    {
        $this->info("\n🔧 Fixing class status...");
        
        $inactiveClasses = Classroom::where('is_active', false)->get();
        
        if ($inactiveClasses->isEmpty()) {
            $this->info('✅ All classes are already active.');
            return;
        }
        
        $this->info("Found {$inactiveClasses->count()} inactive classes.");
        
        foreach ($inactiveClasses as $class) {
            $class->update(['is_active' => true]);
            $this->line("  • Activated {$class->course_code}");
        }
        
        $this->info('✅ Class status fixed.');
    }
    
    private function cleanupOrphanedRecords()
    {
        $this->info("\n🔧 Cleaning up orphaned records...");
        
        // Remove orphaned class-student assignments
        $orphanedAssignments = DB::table('class_student as cs')
            ->leftJoin('classrooms as c', 'cs.classroom_id', '=', 'c.id')
            ->leftJoin('students as s', 'cs.student_id', '=', 's.id')
            ->whereNull('c.id')
            ->orWhereNull('s.id')
            ->delete();
        
        if ($orphanedAssignments > 0) {
            $this->info("✅ Removed {$orphanedAssignments} orphaned class-student assignments.");
        } else {
            $this->info('✅ No orphaned records found.');
        }
    }
} 