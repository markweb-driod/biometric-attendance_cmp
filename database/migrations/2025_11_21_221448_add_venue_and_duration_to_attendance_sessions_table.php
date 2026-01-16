<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->integer('duration')->nullable()->comment('Duration in minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
            $table->dropColumn(['venue_id', 'duration']);
        });
    }
};
