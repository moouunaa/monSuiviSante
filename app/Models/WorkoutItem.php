<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_id',
        'exercise_type',
        'exercise_id',
        'duration',
        'sets',
        'reps',
        'calories_burned'
    ];

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise()
    {
        return $this->morphTo('exercise', 'exercise_type', 'exercise_id', 'id', 'id', [
            'exercise' => Exercise::class,
            'custom_exercise' => CustomExercise::class
        ]);
    }
}