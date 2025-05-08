<?php

namespace App\Strategies;

use App\Models\UserProfile;

class HarrisBenedictStrategy implements CalorieCalculationStrategy
{
    public function calculateCalories(UserProfile $userProfile): int
    {
        // Harris-Benedict Equation (Revised)
        // Men: BMR = 13.397W + 4.799H - 5.677A + 88.362
        // Women: BMR = 9.247W + 3.098H - 4.330A + 447.593
        
        $weight = $userProfile->weight; // kg
        $height = $userProfile->height; // cm
        $age = $userProfile->age;
        $gender = $userProfile->gender;
        
        if ($gender === 'male') {
            $bmr = 13.397 * $weight + 4.799 * $height - 5.677 * $age + 88.362;
        } else {
            $bmr = 9.247 * $weight + 3.098 * $height - 4.330 * $age + 447.593;
        }
        
        // Apply activity factor
        $activityFactors = [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'very_active' => 1.9
        ];
        
        $activityFactor = $activityFactors[$userProfile->activity_level] ?? 1.2;
        $tdee = $bmr * $activityFactor;
        
        // Adjust based on goal
        switch ($userProfile->goal_type) {
            case 'lose_weight':
                $calories = $tdee - 500;
                break;
            case 'gain_weight':
                $calories = $tdee + 500;
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
        return 'Harris-Benedict';
    }
    
    public function getDescription(): string
    {
        return 'La formule Harris-Benedict est une méthode plus ancienne mais toujours largement utilisée pour calculer le métabolisme de base.';
    }
}