<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('class_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('enrollment_status', ['enrolled', 'dropped', 'completed'])->default('enrolled');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('dropped_at')->nullable();
            $table->timestamps();
            
            $table->unique(['classroom_id', 'student_id']);
            $table->index(['enrollment_status', 'enrolled_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_student');
    }
};
