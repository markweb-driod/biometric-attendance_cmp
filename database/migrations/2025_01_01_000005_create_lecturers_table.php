<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('staff_id', 50)->unique();
            $table->string('phone', 20)->nullable();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('title', 50)->nullable(); // Dr., Prof., etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['staff_id', 'is_active']);
            $table->index('department_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lecturers');
    }
};
