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
        // Fix duplicate status column in attendances table
        if (Schema::hasColumn('attendances', 'status')) {
            // Check if the column has the correct type
            $columnInfo = DB::select("DESCRIBE attendances status");
            if (empty($columnInfo)) {
                Schema::table('attendances', function (Blueprint $table) {
                    $table->enum('status', ['present', 'absent', 'late'])->default('absent');
                });
            }
        } else {
            Schema::table('attendances', function (Blueprint $table) {
                $table->enum('status', ['present', 'absent', 'late'])->default('absent');
            });
        }

        // Fix duplicate academic_level column in students table
        if (!Schema::hasColumn('students', 'academic_level')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('academic_level')->nullable()->after('level');
            });
        }

        // Fix duplicate face_registration_enabled column in students table
        if (!Schema::hasColumn('students', 'face_registration_enabled')) {
            Schema::table('students', function (Blueprint $table) {
                $table->boolean('face_registration_enabled')->default(false)->after('reference_image_path');
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
        // Remove duplicate migration entries for the same batch
        $duplicates = DB::table('migrations')
            ->select('migration', DB::raw('COUNT(*) as count'))
            ->groupBy('migration')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // Keep the first entry and remove the rest
            $ids = DB::table('migrations')
                ->where('migration', $duplicate->migration)
                ->orderBy('id')
                ->pluck('id')
                ->skip(1);

            DB::table('migrations')->whereIn('id', $ids)->delete();
        }
    }
};