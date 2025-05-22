<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFood extends Model
{
    use HasFactory;

    protected $table = 'custom_foods';

    protected $fillable = [
        'user_id',
        'name',
        'calories_per_100g',
        'protein_per_100g',
        'carbs_per_100g',
        'fat_per_100g',
        'serving_size',
        'calories_per_serving'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mealItems()
    {
        return $this->hasMany(MealItem::class, 'food_id')->where('food_type', 'custom_food');
    }

    public function calculateCalories($quantity, $unit)
    {
        if ($unit === 'serving' && $this->calories_per_serving) {
            return $this->calories_per_serving * $quantity;
        } else {
            // Assume unit is grams
            return ($this->calories_per_100g / 100) * $quantity;
        }
    }
}