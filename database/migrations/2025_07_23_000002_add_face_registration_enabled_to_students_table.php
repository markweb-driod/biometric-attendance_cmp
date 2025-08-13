<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'face_registration_enabled')) {
                $table->boolean('face_registration_enabled')->default(false)->after('reference_image_path');
            }
        });
    }
    public function down() {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('face_registration_enabled');
        });
    }
};