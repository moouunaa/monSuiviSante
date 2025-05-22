<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('custom_foods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('calories_per_100g');
            $table->float('protein_per_100g')->nullable();
            $table->float('carbs_per_100g')->nullable();
            $table->float('fat_per_100g')->nullable();
            $table->string('serving_size')->nullable();
            $table->integer('calories_per_serving')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_foods');
    }
};