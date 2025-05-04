<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'age',
        'weight',
        'height',
        'goal',
        'plan', // Ajout du plan
        'bmi',
        'daily_calorie_target',
        'protein_target',
        'carbs_target',
        'fat_target',
        'water_target',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}