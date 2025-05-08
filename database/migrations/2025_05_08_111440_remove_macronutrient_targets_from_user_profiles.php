<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['protein_target', 'carbs_target', 'fat_target']);
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->integer('protein_target')->nullable();
            $table->integer('carbs_target')->nullable();
            $table->integer('fat_target')->nullable();
        });
    }
};