<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('gender');
            $table->integer('age');
            $table->decimal('weight', 5, 2);
            $table->integer('height');
            $table->string('goal');
            $table->string('plan');
            $table->decimal('bmi', 5, 2);
            $table->integer('daily_calorie_target');
            $table->integer('water_target');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
