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
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->constrained()->onDelete('set null');
            $table->index(['semester_id', 'captured_at']);
            $table->index(['student_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropIndex(['semester_id', 'captured_at']);
            $table->dropIndex(['student_id', 'semester_id']);
            $table->dropColumn('semester_id');
        });
    }
};