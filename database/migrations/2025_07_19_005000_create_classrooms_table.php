<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('class_name', 255);
            $table->string('course_code', 50);
            $table->string('pin', 20)->unique();
            $table->string('schedule', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('level', 10)->nullable();
            $table->foreignId('lecturer_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['course_code', 'level']);
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('classrooms');
    }
}; 