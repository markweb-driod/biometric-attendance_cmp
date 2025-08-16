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
            $table->string('matric_number', 50)->unique();
            $table->string('full_name', 255);
            $table->string('email', 255)->unique()->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('academic_level', 10)->nullable(); // 100, 200, 300, 400, etc.
            $table->string('department', 100)->nullable();
            $table->string('level', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('reference_image_path', 500)->nullable();
            $table->boolean('face_registration_enabled')->default(false);
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['academic_level', 'department']);
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};