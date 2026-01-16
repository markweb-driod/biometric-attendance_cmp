<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix any existing attendance sessions that might be missing session_name
        AttendanceSession::whereNull('session_name')->chunk(100, function ($sessions) {
            foreach ($sessions as $session) {
                $classroom = Classroom::find($session->classroom_id);
                $sessionName = $classroom ? 
                    $classroom->class_name . ' - ' . Carbon::parse($session->start_time)->format('M d, Y') : 
                    'Attendance Session - ' . Carbon::parse($session->start_time)->format('M d, Y');
                
                $session->update(['session_name' => $sessionName]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this data fix
    }
};

