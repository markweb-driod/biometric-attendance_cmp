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
        // Fix duplicate status column in attendances table - use string instead of enum for compatibility
        if (!Schema::hasColumn('attendances', 'status')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->string('status')->default('absent');
            });
        }

        // Fix duplicate academic_level column in students table
        if (!Schema::hasColumn('students', 'academic_level')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('academic_level')->nullable();
            });
        }

        // Fix duplicate face_registration_enabled column in students table
        if (!Schema::hasColumn('students', 'face_registration_enabled')) {
            Schema::table('students', function (Blueprint $table) {
                $table->boolean('face_registration_enabled')->default(false);
            });
        }

        // Clean up duplicate migration entries from migrations table
        $this->cleanMigrationEntries();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a fix migration, no down operation needed
    }

    /**
     * Clean up duplicate migration entries
     */
    private function cleanMigrationEntries(): void
    {
        // This is a no-op for Oracle Cloud deployment
        // The migration system will handle duplicates automatically
    }
};