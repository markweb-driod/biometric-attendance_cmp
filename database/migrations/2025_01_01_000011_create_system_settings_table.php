<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value');
            $table->string('description', 500)->nullable();
            $table->enum('type', ['string', 'integer', 'boolean', 'json'])->default('string');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            $table->index(['setting_key', 'is_public']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};
