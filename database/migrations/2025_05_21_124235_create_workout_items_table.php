<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->onDelete('cascade');
            $table->morphs('exercise'); // Pour supporter à la fois Exercise et CustomExercise
            $table->integer('duration'); // en minutes
            $table->integer('sets')->nullable();
            $table->integer('reps')->nullable();
            $table->integer('calories_burned');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workout_items');
    }
};