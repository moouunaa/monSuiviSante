<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Exercise;
use App\Models\CustomExercise;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Register morph map for polymorphic relationships
        Relation::morphMap([
            'exercise' => Exercise::class,
            'custom_exercise' => CustomExercise::class,
        ]);
    }
}
