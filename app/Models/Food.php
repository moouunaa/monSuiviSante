<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'name',
        'calories_per_100g',
        'protein_per_100g',
        'carbs_per_100g',
        'fat_per_100g',
        'serving_size',
        'calories_per_serving',
        'is_default'
    ];

    public function mealItems()
    {
        return $this->hasMany(MealItem::class, 'food_id')->where('food_type', 'food');
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