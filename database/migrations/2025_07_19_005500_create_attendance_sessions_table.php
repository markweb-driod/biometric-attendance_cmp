<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classroom_id');
            $table->string('session_name', 255);
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->string('status', 20)->default('active'); // active, paused, ended
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['classroom_id', 'status']);
            $table->index(['start_time', 'end_time']);
            $table->index('is_active');
        });
        
        // Add foreign key constraint after table is created and classrooms table exists
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_sessions');
    }
}; 