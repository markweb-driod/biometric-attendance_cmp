<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->string('image_path', 500);
            $table->timestamp('captured_at');
            $table->text('notes')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('status', 20)->default('pending'); // present/denied/absent
            $table->unsignedBigInteger('attendance_session_id')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['student_id', 'classroom_id']);
            $table->index(['captured_at', 'status']);
            $table->index('attendance_session_id');
        });
        
        // Add foreign key constraint after table is created and attendance_sessions table exists
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('attendance_session_id')->references('id')->on('attendance_sessions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}; 