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
        // Students table indexes
        Schema::table('students', function (Blueprint $table) {
            if (!$this->indexExists('students', 'students_matric_number_index')) {
                try { $table->index('matric_number'); } catch (\Exception $e) {}
            }
            if (!$this->indexExists('students', 'students_department_id_index')) {
                try { $table->index('department_id'); } catch (\Exception $e) {}
            }
            if (!$this->indexExists('students', 'students_academic_level_id_index')) {
                try { $table->index('academic_level_id'); } catch (\Exception $e) {}
            }
            if (!$this->indexExists('students', 'students_is_active_index')) {
                try { $table->index('is_active'); } catch (\Exception $e) {}
            }
            if (!$this->indexExists('students', 'students_created_at_index')) {
                try { $table->index('created_at'); } catch (\Exception $e) {}
            }
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_email_index')) {
                $table->index('email');
            }
            if (!$this->indexExists('users', 'users_role_index')) {
                $table->index('role');
            }
            if (!$this->indexExists('users', 'users_is_active_index')) {
                $table->index('is_active');
            }
        });

        // Classrooms table indexes
        Schema::table('classrooms', function (Blueprint $table) {
            if (!$this->indexExists('classrooms', 'classrooms_course_id_index')) {
                $table->index('course_id');
            }
            if (!$this->indexExists('classrooms', 'classrooms_lecturer_id_index')) {
                $table->index('lecturer_id');
            }
            if (!$this->indexExists('classrooms', 'classrooms_is_active_index')) {
                $table->index('is_active');
            }
            if (!$this->indexExists('classrooms', 'classrooms_created_at_index')) {
                $table->index('created_at');
            }
        });

        // Attendances table indexes
        Schema::table('attendances', function (Blueprint $table) {
            if (!$this->indexExists('attendances', 'attendances_student_id_index')) {
                $table->index('student_id');
            }
            if (!$this->indexExists('attendances', 'attendances_classroom_id_index')) {
                $table->index('classroom_id');
            }
            if (!$this->indexExists('attendances', 'attendances_attendance_session_id_index')) {
                $table->index('attendance_session_id');
            }
            if (!$this->indexExists('attendances', 'attendances_captured_at_index')) {
                $table->index('captured_at');
            }
            if (!$this->indexExists('attendances', 'attendances_status_index')) {
                $table->index('status');
            }
        });

        // Attendance sessions table indexes
        Schema::table('attendance_sessions', function (Blueprint $table) {
            if (!$this->indexExists('attendance_sessions', 'attendance_sessions_classroom_id_index')) {
                $table->index('classroom_id');
            }
            // Only add lecturer_id index if the column exists
            if (Schema::hasColumn('attendance_sessions', 'lecturer_id') && 
                !$this->indexExists('attendance_sessions', 'attendance_sessions_lecturer_id_index')) {
                $table->index('lecturer_id');
            }
            if (!$this->indexExists('attendance_sessions', 'attendance_sessions_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->indexExists('attendance_sessions', 'attendance_sessions_status_index')) {
                $table->index('status');
            }
        });

        // Departments table indexes
        Schema::table('departments', function (Blueprint $table) {
            if (!$this->indexExists('departments', 'departments_is_active_index')) {
                $table->index('is_active');
            }
        });

        // Courses table indexes
        Schema::table('courses', function (Blueprint $table) {
            if (!$this->indexExists('courses', 'courses_department_id_index')) {
                $table->index('department_id');
            }
            if (!$this->indexExists('courses', 'courses_academic_level_id_index')) {
                $table->index('academic_level_id');
            }
            if (!$this->indexExists('courses', 'courses_is_active_index')) {
                $table->index('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['matric_number']);
            $table->dropIndex(['department_id']);
            $table->dropIndex(['academic_level_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropIndex(['course_id']);
            $table->dropIndex(['lecturer_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['classroom_id']);
            $table->dropIndex(['attendance_session_id']);
            $table->dropIndex(['captured_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropIndex(['classroom_id']);
            $table->dropIndex(['lecturer_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['department_id']);
            $table->dropIndex(['academic_level_id']);
            $table->dropIndex(['is_active']);
        });
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
};