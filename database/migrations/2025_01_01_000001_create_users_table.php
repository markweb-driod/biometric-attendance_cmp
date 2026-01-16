<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 255)->unique();
            $table->string('full_name', 255);
            $table->string('password');
            $table->enum('role', ['student', 'lecturer', 'superadmin']);
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index(['role', 'is_active']);
            $table->index('username');
            $table->index('email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
