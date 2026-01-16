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
        Schema::create('api_key_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->constrained('api_keys')->onDelete('cascade');
            $table->string('endpoint');
            $table->string('method', 10)->default('GET');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->integer('response_status')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->json('request_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at');
            
            $table->index('api_key_id');
            $table->index('created_at');
            $table->index('endpoint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_key_logs');
    }
};
