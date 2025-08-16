<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'reference_image_path')) {
                $table->string('reference_image_path')->nullable()->after('matric_number');
            }
        });
    }
    public function down() {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'reference_image_path')) {
                $table->dropColumn('reference_image_path');
            }
        });
    }
}; 