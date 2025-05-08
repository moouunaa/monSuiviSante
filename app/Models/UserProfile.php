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
        'plan',
        'activity_level', // Ajout du niveau d'activité
        'body_fat_percentage', // Ajout du pourcentage de graisse corporelle
        'bmi',
        'daily_calorie_target',
        'water_target',
    ];

    protected $casts = [
        'age' => 'integer',
        'weight' => 'float',
        'height' => 'float',
        'body_fat_percentage' => 'float',
        'bmi' => 'float',
        'daily_calorie_target' => 'integer',
        'water_target' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}