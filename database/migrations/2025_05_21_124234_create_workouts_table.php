<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->date('entry_date');
            $table->time('entry_time');
            $table->integer('total_duration');
            $table->integer('total_calories_burned');
            $table->text('notes')->nullable();
            $table->boolean('is_template')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workouts');
    }
};