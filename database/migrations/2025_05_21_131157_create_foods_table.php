<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('calories_per_100g');
            $table->float('protein_per_100g')->nullable();
            $table->float('carbs_per_100g')->nullable();
            $table->float('fat_per_100g')->nullable();
            $table->string('serving_size')->nullable();
            $table->integer('calories_per_serving')->nullable();
            $table->boolean('is_default')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('foods');
    }
};