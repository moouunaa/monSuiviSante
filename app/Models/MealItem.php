<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_id',
        'food_type',
        'food_id',
        'quantity',
        'unit',
        'calories'
    ];

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    public function food()
    {
        if ($this->food_type === 'food') {
            return $this->belongsTo(Food::class, 'food_id')->withDefault([
                'name' => 'Aliment supprimé'
            ]);
        } else {
            return $this->belongsTo(CustomFood::class, 'food_id')->withDefault([
                'name' => 'Aliment personnalisé supprimé'
            ]);
        }
    }
}