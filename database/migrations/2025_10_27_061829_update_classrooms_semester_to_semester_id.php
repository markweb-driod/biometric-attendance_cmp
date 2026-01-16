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
        Schema::table('classrooms', function (Blueprint $table) {
            // Add the new semester_id column
            $table->foreignId('semester_id')->nullable()->constrained()->onDelete('set null');
            
            // Add index for the new column
            $table->index(['semester_id', 'is_active']);
        });
        
        // Note: We'll keep the old 'semester' column for now to avoid data loss
        // It can be dropped in a future migration after data migration is complete
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropIndex(['semester_id', 'is_active']);
            $table->dropColumn('semester_id');
        });
    }
};