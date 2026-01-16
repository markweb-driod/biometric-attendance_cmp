<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('matric_number', 50)->unique();
            $table->string('phone', 20)->nullable();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_level_id')->constrained()->onDelete('cascade');
            $table->string('reference_image_path', 500)->nullable();
            $table->boolean('face_registration_enabled')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['matric_number', 'is_active']);
            $table->index(['department_id', 'academic_level_id']);
            $table->index('face_registration_enabled');
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};
