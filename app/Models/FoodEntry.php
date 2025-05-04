<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'food_name',
        'meal_type',
        'calories',
        'portion_size',
        'entry_date',
        'entry_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}