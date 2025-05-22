<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'height',
        'gender',
        'birth_date',
        'activity_level',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
    ];

    /**
     * Get the user's weight entries.
     */
    public function weightEntries()
    {
        return $this->hasMany(WeightEntry::class);
    }

    /**
     * Get the user's goals.
     */
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * Get the user's food entries.
     */
    public function foodEntries()
    {
        return $this->hasMany(FoodEntry::class);
    }

    /**
     * Get the user's exercise entries.
     */
    public function exerciseEntries()
    {
        return $this->hasMany(ExerciseEntry::class);
    }

    /**
     * Get the user's water entries.
     */
    public function waterEntries()
    {
        return $this->hasMany(WaterEntry::class);
    }

    /**
     * Get the user's sleep entries.
     */
    public function sleepEntries()
    {
        return $this->hasMany(SleepEntry::class);
    }
    
    /**
     * Get the user's meals.
     */
    public function meals()
    {
        return $this->hasMany(Meal::class);
    }
    
    /**
     * Get the user's workouts.
     */
    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }
    
    /**
     * Get the user's custom foods.
     */
    public function customFoods()
    {
        return $this->hasMany(CustomFood::class);
    }
    
    /**
     * Get the user's custom exercises.
     */
    public function customExercises()
    {
        return $this->hasMany(CustomExercise::class);
    }

    /**
     * Get the user's current weight.
     */
    public function getCurrentWeight()
    {
        $latestEntry = $this->weightEntries()->latest('entry_date')->first();
        return $latestEntry ? $latestEntry->weight : null;
    }

    /**
     * Calculate the user's BMI.
     */
    public function calculateBMI()
    {
        $weight = $this->getCurrentWeight();
        $height = $this->height;

        if (!$weight || !$height) {
            return null;
        }

        // BMI = weight (kg) / (height (m))^2
        $heightInMeters = $height / 100;
        return round($weight / ($heightInMeters * $heightInMeters), 1);
    }

    /**
     * Calculate the user's BMR (Basal Metabolic Rate) using the Mifflin-St Jeor Equation.
     */
    public function calculateBMR()
    {
        $weight = $this->getCurrentWeight();
        $height = $this->height;
        $age = $this->birth_date ? $this->birth_date->age : null;
        $gender = $this->gender;

        if (!$weight || !$height || !$age || !$gender) {
            return null;
        }

        if ($gender === 'male') {
            return round((10 * $weight) + (6.25 * $height) - (5 * $age) + 5);
        } else {
            return round((10 * $weight) + (6.25 * $height) - (5 * $age) - 161);
        }
    }

    /**
     * Calculate the user's TDEE (Total Daily Energy Expenditure).
     */
    public function calculateTDEE()
    {
        $bmr = $this->calculateBMR();
        $activityLevel = $this->activity_level;

        if (!$bmr || !$activityLevel) {
            return null;
        }

        $activityMultipliers = [
            'sedentary' => 1.2,
            'lightly_active' => 1.375,
            'moderately_active' => 1.55,
            'very_active' => 1.725,
            'extra_active' => 1.9,
        ];

        $multiplier = $activityMultipliers[$activityLevel] ?? 1.2;

        return round($bmr * $multiplier);
    }
}