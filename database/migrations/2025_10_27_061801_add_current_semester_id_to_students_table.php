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
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('current_semester_id')->nullable()->constrained('semesters')->onDelete('set null');
            $table->index(['current_semester_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['current_semester_id']);
            $table->dropIndex(['current_semester_id', 'is_active']);
            $table->dropColumn('current_semester_id');
        });
    }
};