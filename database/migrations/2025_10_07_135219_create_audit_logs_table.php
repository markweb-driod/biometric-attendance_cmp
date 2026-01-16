<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // login, logout, view, create, update, delete, export, etc.
            $table->string('resource_type'); // student, lecturer, attendance, exam_eligibility, etc.
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('description');
            $table->json('old_values')->nullable(); // Store previous values for updates
            $table->json('new_values')->nullable(); // Store new values for updates
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->string('user_type')->nullable(); // User type (HOD, Lecturer, etc.)
            $table->unsignedBigInteger('user_id')->nullable(); // User ID
            $table->string('department_id')->nullable(); // Track department context
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->index(['action', 'created_at']);
            $table->index(['resource_type', 'resource_id']);
            $table->index(['department_id', 'created_at']);
            $table->index('severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
