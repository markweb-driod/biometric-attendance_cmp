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
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained()->onDelete('cascade');
            $table->string('pin', 20)->unique();
            $table->string('schedule', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('semester', 20)->nullable();
            $table->string('academic_year', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['pin', 'is_active']);
            $table->index(['course_id', 'lecturer_id']);
            $table->index(['semester', 'academic_year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('classrooms');
    }
};
