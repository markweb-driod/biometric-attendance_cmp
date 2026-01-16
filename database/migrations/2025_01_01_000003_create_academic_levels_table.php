<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('academic_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique(); // e.g., "100 Level", "200 Level"
            $table->string('code', 10)->unique(); // e.g., "100", "200"
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['code', 'sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('academic_levels');
    }
};
