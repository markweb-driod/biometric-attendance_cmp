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
            $table->foreignId('attendance_session_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('image_path', 500)->nullable();
            $table->timestamp('captured_at');
            $table->enum('status', ['present', 'absent', 'late', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('device_info', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'classroom_id']);
            $table->index(['captured_at', 'status']);
            $table->index('attendance_session_id');
            $table->index(['status', 'captured_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
