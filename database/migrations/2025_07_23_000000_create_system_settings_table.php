<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value');
            $table->string('description', 500)->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('setting_key');
        });
    }
    public function down() {
        Schema::dropIfExists('system_settings');
    }
}; 