<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('food_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('food_name');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack']);
            $table->integer('calories');
            $table->string('portion_size');
            $table->float('protein')->nullable();
            $table->float('carbs')->nullable();
            $table->float('fat')->nullable();
            $table->text('notes')->nullable();
            $table->date('entry_date');
            $table->time('entry_time');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('food_entries');
    }
};