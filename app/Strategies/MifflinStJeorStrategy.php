<?php

namespace App\Strategies;

use App\Models\UserProfile;

class MifflinStJeorStrategy implements CalorieCalculationStrategy
{
    public function calculateCalories(UserProfile $userProfile): int
    {
        // Mifflin-St Jeor Equation
        // Men: BMR = 10W + 6.25H - 5A + 5
        // Women: BMR = 10W + 6.25H - 5A - 161
        
        $weight = $userProfile->weight; // kg
        $height = $userProfile->height; // cm
        $age = $userProfile->age;
        $gender = $userProfile->gender;
        
        if ($gender === 'male') {
            $bmr = 10 * $weight + 6.25 * $height - 5 * $age + 5;
        } else {
            $bmr = 10 * $weight + 6.25 * $height - 5 * $age - 161;
        }
        
        // Apply activity factor
        $activityFactors = [
            'sedentary' => 1.2, // Little or no exercise
            'light' => 1.375, // Light exercise 1-3 days/week
            'moderate' => 1.55, // Moderate exercise 3-5 days/week
            'active' => 1.725, // Hard exercise 6-7 days/week
            'very_active' => 1.9 // Very hard exercise & physical job
        ];
        
        $activityFactor = $activityFactors[$userProfile->activity_level] ?? 1.2;
        $tdee = $bmr * $activityFactor;
        
        // Adjust based on goal
        switch ($userProfile->goal_type) {
            case 'lose_weight':
                $calories = $tdee - 500; // 500 calorie deficit
                break;
            case 'gain_weight':
                $calories = $tdee + 500; // 500 calorie surplus
                break;
            case 'maintain_weight':
            default:
                $calories = $tdee;
                break;
        }
        
        return round($calories);
    }
    
    public function getName(): string
    {
        return 'Mifflin-St Jeor';
    }
    
    public function getDescription(): string
    {
        return 'La formule Mifflin-St Jeor est considérée comme la plus précise pour estimer le métabolisme de base (BMR).';
    }
}