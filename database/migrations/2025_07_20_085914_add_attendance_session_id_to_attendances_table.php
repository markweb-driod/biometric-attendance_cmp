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
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'attendance_session_id')) {
                $table->unsignedBigInteger('attendance_session_id')->nullable()->after('id');
                // Uncomment below to add a foreign key constraint if needed:
                // $table->foreign('attendance_session_id')->references('id')->on('attendance_sessions')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('attendance_session_id');
        });
    }
};
