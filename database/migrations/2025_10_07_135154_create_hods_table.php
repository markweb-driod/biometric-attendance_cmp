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
        Schema::create('hods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('staff_id')->unique();
            $table->string('title')->nullable(); // Dr., Prof., etc.
            $table->string('phone')->nullable();
            $table->string('office_location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('appointed_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->json('permissions')->nullable(); // Store HOD-specific permissions
            $table->timestamps();
            
            $table->index(['department_id', 'is_active']);
            $table->index('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hods');
    }
};
