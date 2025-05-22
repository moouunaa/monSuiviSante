<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'meal_type',
        'entry_date',
        'entry_time',
        'total_calories',
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

    public function mealItems()
    {
        return $this->hasMany(MealItem::class);
    }

    // Prototype Pattern - Clone a meal
    public function cloneMeal($newDate = null, $newTime = null)
    {
        $clone = $this->replicate();
        $clone->entry_date = $newDate ?? now()->format('Y-m-d');
        $clone->entry_time = $newTime ?? now()->format('H:i');
        $clone->is_template = false;
        $clone->save();

        // Clone all meal items
        foreach ($this->mealItems as $item) {
            $newItem = $item->replicate();
            $newItem->meal_id = $clone->id;
            $newItem->save();
        }

        return $clone;
    }

    // Calculate total calories from all items
    public function calculateTotalCalories()
    {
        return $this->mealItems->sum('calories');
    }

    // Update total calories
    public function updateTotalCalories()
    {
        $this->total_calories = $this->calculateTotalCalories();
        $this->save();
    }
}