<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'entry_date',
        'entry_time',
        'total_duration',
        'total_calories_burned',
        'notes',
        'is_template'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'is_template' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(WorkoutItem::class);
    }

    public function workoutItems()
    {
        return $this->hasMany(WorkoutItem::class);
    }

    // Prototype Pattern - Clone a workout
    public function cloneWorkout($newDate = null, $newTime = null)
    {
        $clone = $this->replicate();
        $clone->entry_date = $newDate ?? now()->format('Y-m-d');
        $clone->entry_time = $newTime ?? now()->format('H:i');
        $clone->is_template = false;
        $clone->save();

        // Clone all workout items
        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $newItem->workout_id = $clone->id;
            $newItem->save();
        }

        return $clone;
    }

    // Calculate total duration
    public function calculateTotalDuration()
    {
        return $this->items->sum('duration');
    }

    // Calculate total calories burned
    public function calculateTotalCaloriesBurned()
    {
        return $this->items->sum('calories_burned');
    }

    // Update totals
    public function updateTotals()
    {
        $this->total_duration = $this->calculateTotalDuration();
        $this->total_calories_burned = $this->calculateTotalCaloriesBurned();
        $this->save();
    }
}