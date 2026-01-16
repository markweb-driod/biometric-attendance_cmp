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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique()->index();
            $table->enum('user_type', ['Superadmin', 'Lecturer', 'Hod'])->index();
            $table->unsignedBigInteger('user_id');
            $table->string('identifier'); // email for superadmin, staff_id for lecturer/hod
            $table->string('full_name')->nullable();
            
            // Session metadata
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->enum('status', ['active', 'ended', 'expired', 'terminated'])->default('active')->index();
            
            // Device and location data
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('timezone')->nullable();
            
            // Activity trail (JSON)
            $table->json('activity_trail')->nullable();
            
            // Department context for lecturer/hod
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('department_name')->nullable();
            
            // Termination info
            $table->unsignedBigInteger('terminated_by')->nullable();
            $table->timestamp('terminated_at')->nullable();
            $table->text('termination_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_type', 'user_id']);
            $table->index(['status', 'login_at']);
            $table->index('login_at');
            $table->index('last_activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
