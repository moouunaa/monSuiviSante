<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'calories_per_minute',
        'category',
        'is_default'
    ];

    public function workoutItems()
    {
        return $this->morphMany(WorkoutItem::class, 'exercise', 'exercise_type', 'exercise_id');
    }

    public function calculateCaloriesBurned($duration)
    {
        return $this->calories_per_minute * $duration;
    }
}