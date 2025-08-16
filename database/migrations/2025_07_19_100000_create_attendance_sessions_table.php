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
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
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
    }

    public function down()
    {
        Schema::dropIfExists('attendance_sessions');
    }
}; 