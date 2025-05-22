<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack'])->default('snack');
            $table->integer('total_calories');
            $table->date('entry_date');
            $table->time('entry_time');
            $table->text('notes')->nullable();
            $table->boolean('is_template')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meals');
    }
};
