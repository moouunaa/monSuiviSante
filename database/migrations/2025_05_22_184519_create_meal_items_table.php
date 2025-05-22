<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_id')->constrained()->onDelete('cascade');
            $table->enum('food_type', ['food', 'custom_food']);
            $table->unsignedBigInteger('food_id');
            $table->float('quantity');
            $table->string('unit');
            $table->integer('calories');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meal_items');
    }
};
