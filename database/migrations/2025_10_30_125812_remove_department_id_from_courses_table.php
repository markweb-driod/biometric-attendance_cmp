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
        // Backfill pivot from existing courses.department_id before dropping
        $courses = DB::table('courses')->select('id as course_id', 'department_id')->whereNotNull('department_id')->get();
        foreach ($courses as $course) {
            if ($course->department_id) {
                $exists = DB::table('course_department')
                    ->where('course_id', $course->course_id)
                    ->where('department_id', $course->department_id)
                    ->exists();
                if (!$exists) {
                    DB::table('course_department')->insert([
                        'course_id' => $course->course_id,
                        'department_id' => $course->department_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Attempt to drop related indexes safely (SQLite-friendly)
        try { DB::statement('DROP INDEX IF EXISTS courses_department_id_academic_level_id_index'); } catch (\Throwable $e) {}
        try { DB::statement('DROP INDEX IF EXISTS courses_department_id_index'); } catch (\Throwable $e) {}

        Schema::table('courses', function (Blueprint $table) {
            // Drop foreign and column
            try {
                $table->dropForeign(['department_id']);
            } catch (\Throwable $e) {
                // ignore if foreign missing
            }
            try {
                $table->dropColumn('department_id');
            } catch (\Throwable $e) {
                // ignore if already dropped
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade');
                try {
                    $table->index(['department_id', 'academic_level_id']);
                } catch (\Throwable $e) {}
            }
        });
    }
};
