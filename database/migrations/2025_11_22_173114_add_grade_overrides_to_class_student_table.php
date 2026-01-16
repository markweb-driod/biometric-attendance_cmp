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
        Schema::table('class_student', function (Blueprint $table) {
            $table->string('grade_override', 2)->nullable()->after('id');
            $table->text('override_reason')->nullable()->after('grade_override');
            $table->unsignedBigInteger('overridden_by')->nullable()->after('override_reason');
            $table->timestamp('overridden_at')->nullable()->after('overridden_by');
            
            $table->foreign('overridden_by')->references('id')->on('lecturers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_student', function (Blueprint $table) {
            $table->dropForeign(['overridden_by']);
            $table->dropColumn(['grade_override', 'override_reason', 'overridden_by', 'overridden_at']);
        });
    }
};
