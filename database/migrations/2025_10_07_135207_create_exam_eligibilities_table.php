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
        Schema::create('exam_eligibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('semester');
            $table->string('academic_year');
            $table->decimal('attendance_percentage', 5, 2);
            $table->decimal('required_threshold', 5, 2)->default(75.00);
            $table->enum('status', ['eligible', 'ineligible', 'overridden'])->default('ineligible');
            $table->text('override_reason')->nullable();
            $table->foreignId('overridden_by')->nullable()->constrained('hods')->onDelete('set null');
            $table->timestamp('overridden_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->json('validation_details')->nullable(); // Store detailed validation results
            $table->timestamps();
            
            $table->unique(['student_id', 'course_id', 'semester', 'academic_year'], 'exam_elig_unique');
            $table->index(['status', 'semester', 'academic_year'], 'exam_elig_status_idx');
            $table->index(['attendance_percentage', 'required_threshold'], 'exam_elig_percentage_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_eligibilities');
    }
};
