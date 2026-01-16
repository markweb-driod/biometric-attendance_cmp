<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code', 20)->unique();
            $table->string('course_name', 255);
            $table->text('description')->nullable();
            $table->integer('credit_units')->default(3);
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_level_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['course_code', 'is_active']);
            $table->index(['department_id', 'academic_level_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
};
