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
        Schema::table('attendance_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_sessions', 'code')) {
                $table->string('code', 10)->nullable()->after('lecturer_id');
            }
        });
        
        // Generate codes for existing sessions
        \App\Models\AttendanceSession::whereNull('code')->chunk(100, function ($sessions) {
            foreach ($sessions as $session) {
                do {
                    $code = strtoupper(\Illuminate\Support\Str::random(6));
                } while (\App\Models\AttendanceSession::where('code', $code)->exists());
                $session->update(['code' => $code]);
            }
        });
        
        // Make the column unique and not nullable
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->string('code', 10)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
