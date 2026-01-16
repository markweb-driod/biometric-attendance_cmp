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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // e.g., "First Semester", "Second Semester"
            $table->string('code', 10)->unique(); // e.g., "SEM1", "SEM2"
            $table->string('academic_year', 10); // e.g., "2024/2025"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_current')->default(false); // Only one semester can be current
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['academic_year', 'code']);
            $table->index(['is_active', 'is_current']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};