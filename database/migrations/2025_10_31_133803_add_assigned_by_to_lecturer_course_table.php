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
        Schema::table('lecturer_course', function (Blueprint $table) {
            $table->string('assigned_by_type')->nullable()->after('assigned_at');
            $table->unsignedBigInteger('assigned_by_id')->nullable()->after('assigned_by_type');
            $table->index(['assigned_by_type', 'assigned_by_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lecturer_course', function (Blueprint $table) {
            $table->dropIndex(['assigned_by_type', 'assigned_by_id']);
            $table->dropColumn(['assigned_by_type', 'assigned_by_id']);
        });
    }
};
