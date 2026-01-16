<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add composite indexes for attendance queries
        $this->addCompositeIndex('attendances', ['student_id', 'attendance_session_id'], 'attendances_student_session_idx');
        $this->addCompositeIndex('attendances', ['classroom_id', 'captured_at'], 'attendances_classroom_date_idx');
        $this->addCompositeIndex('attendances', ['status', 'captured_at'], 'attendances_status_date_idx');
        
        // Add indexes for attendance sessions
        $this->addCompositeIndex('attendance_sessions', ['session_name', 'is_active'], 'sessions_name_active_idx');
        $this->addCompositeIndex('attendance_sessions', ['classroom_id', 'is_active'], 'sessions_classroom_active_idx');
        $this->addCompositeIndex('attendance_sessions', ['status', 'is_active'], 'sessions_status_active_idx');
        
        // Add indexes for students
        $this->addCompositeIndex('students', ['matric_number', 'is_active'], 'students_matric_active_idx');
        $this->addCompositeIndex('students', ['user_id', 'is_active'], 'students_user_active_idx');
        
        // Add indexes for classrooms
        $this->addCompositeIndex('classrooms', ['course_id', 'is_active'], 'classrooms_course_active_idx');
        $this->addCompositeIndex('classrooms', ['lecturer_id', 'is_active'], 'classrooms_lecturer_active_idx');
        
        // Add indexes for class_student pivot table
        $this->addCompositeIndex('class_student', ['student_id', 'classroom_id'], 'class_student_pivot_idx');
        
        // Add partial indexes for active records only
        $this->addPartialIndex('students', 'matric_number', 'students_active_matric_idx', 'is_active = 1');
        $this->addPartialIndex('lecturers', 'staff_id', 'lecturers_active_staff_idx', 'is_active = 1');
        $this->addPartialIndex('attendance_sessions', 'session_name', 'sessions_active_name_idx', 'is_active = 1');
        
        // Add covering indexes for common queries
        $this->addCoveringIndex('attendances', 
            ['student_id', 'attendance_session_id', 'status', 'captured_at'], 
            'attendances_covering_idx'
        );
        
        $this->addCoveringIndex('students', 
            ['matric_number', 'user_id', 'is_active'], 
            'students_covering_idx'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all indexes created in up()
        $indexes = [
            'attendances_student_session_idx',
            'attendances_classroom_date_idx',
            'attendances_status_date_idx',
            'sessions_name_active_idx',
            'sessions_classroom_active_idx',
            'sessions_status_active_idx',
            'students_matric_active_idx',
            'students_user_active_idx',
            'classrooms_course_active_idx',
            'classrooms_lecturer_active_idx',
            'class_student_pivot_idx',
            'students_active_matric_idx',
            'lecturers_active_staff_idx',
            'sessions_active_name_idx',
            'attendances_covering_idx',
            'students_covering_idx'
        ];
        
        foreach ($indexes as $index) {
            $this->dropIndexIfExists($index);
        }
    }
    
    /**
     * Add composite index with SQLite compatibility
     */
    private function addCompositeIndex($table, $columns, $indexName)
    {
        if (!$this->indexExists($table, $indexName)) {
            $columnList = implode(', ', $columns);
            DB::statement("CREATE INDEX {$indexName} ON {$table} ({$columnList})");
        }
    }
    
    /**
     * Add partial index (MySQL compatible - creates regular index since MySQL doesn't support WHERE clause in index definition)
     */
    private function addPartialIndex($table, $column, $indexName, $whereClause)
    {
        if (!$this->indexExists($table, $indexName)) {
            // MySQL doesn't support partial indexes with WHERE clause, so we create a regular index
            // For MySQL 8.0+, we could use functional indexes, but for compatibility we'll use regular index
            $driver = DB::connection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement("CREATE INDEX {$indexName} ON {$table} ({$column})");
            } else {
                // SQLite supports WHERE clause
                DB::statement("CREATE INDEX {$indexName} ON {$table} ({$column}) WHERE {$whereClause}");
            }
        }
    }
    
    /**
     * Add covering index
     */
    private function addCoveringIndex($table, $columns, $indexName)
    {
        if (!$this->indexExists($table, $indexName)) {
            $columnList = implode(', ', $columns);
            DB::statement("CREATE INDEX {$indexName} ON {$table} ({$columnList})");
        }
    }
    
    /**
     * Check if index exists (database-agnostic)
     */
    private function indexExists($table, $indexName)
    {
        try {
            $driver = DB::connection()->getDriverName();
            
            if ($driver === 'sqlite') {
                // SQLite method
                $indexes = DB::select("PRAGMA index_list({$table})");
                foreach ($indexes as $index) {
                    if ($index->name === $indexName) {
                        return true;
                    }
                }
            } elseif ($driver === 'mysql') {
                // MySQL method
                $result = DB::select(
                    "SELECT COUNT(*) as count FROM information_schema.statistics 
                     WHERE table_schema = DATABASE() 
                     AND table_name = ? 
                     AND index_name = ?",
                    [$table, $indexName]
                );
                return $result[0]->count > 0;
            } else {
                // Fallback: try to query the index directly
                try {
                    DB::statement("SELECT 1 FROM {$table} USE INDEX ({$indexName}) LIMIT 1");
                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Drop index if it exists
     */
    private function dropIndexIfExists($indexName)
    {
        try {
            DB::statement("DROP INDEX IF EXISTS {$indexName}");
        } catch (\Exception $e) {
            // Index might not exist, ignore error
        }
    }
};