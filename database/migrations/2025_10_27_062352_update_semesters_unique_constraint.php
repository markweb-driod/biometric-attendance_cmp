<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('semesters', function (Blueprint $table) {
            // Drop the unique constraint on code only
            $table->dropUnique(['code']);
            
            // Add a composite unique constraint on code and academic_year
            $table->unique(['code', 'academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('semesters', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique(['code', 'academic_year']);
            
            // Restore the unique constraint on code only
            $table->unique('code');
        });
    }
};