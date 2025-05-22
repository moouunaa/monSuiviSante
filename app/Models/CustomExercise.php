<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'calories_per_minute',
        'category'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workoutItems()
    {
        return $this->morphMany(WorkoutItem::class, 'exercise', 'exercise_type', 'exercise_id');
    }

    public function calculateCaloriesBurned($duration)
    {
        return $this->calories_per_minute * $duration;
    }
}