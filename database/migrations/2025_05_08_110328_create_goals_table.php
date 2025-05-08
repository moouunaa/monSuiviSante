<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'calories', 'water', 'sleep', etc.
            $table->integer('target_value');
            $table->string('calculation_method')->nullable(); // 'mifflin_st_jeor', 'harris_benedict', 'katch_mcardle', 'custom'
            $table->integer('custom_value')->nullable(); // For custom goals
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goals');
    }
};