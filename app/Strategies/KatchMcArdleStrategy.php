<?php

namespace App\Strategies;

use App\Models\UserProfile;

class KatchMcArdleStrategy implements CalorieCalculationStrategy
{
    public function calculateCalories(UserProfile $userProfile): int
    {
        // Katch-McArdle Formula: BMR = 370 + (21.6 * LBM)
        // where LBM is Lean Body Mass in kg
        
        $weight = $userProfile->weight; // kg
        
        // Estimate body fat percentage if not provided
        // These are very rough estimates
        $bodyFatPercentage = $userProfile->body_fat_percentage ?? $this->estimateBodyFat($userProfile);
        
        // Calculate Lean Body Mass
        $lbm = $weight * (1 - ($bodyFatPercentage / 100));
        
        // Calculate BMR using Katch-McArdle
        $bmr = 370 + (21.6 * $lbm);
        
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
    
    private function estimateBodyFat(UserProfile $userProfile): float
    {
        // Very rough estimate based on gender and BMI
        $height = $userProfile->height / 100; // convert cm to m
        $bmi = $userProfile->weight / ($height * $height);
        
        if ($userProfile->gender === 'male') {
            if ($bmi < 18.5) return 10; // underweight
            if ($bmi < 25) return 15;   // normal weight
            if ($bmi < 30) return 25;   // overweight
            return 30;                  // obese
        } else {
            if ($bmi < 18.5) return 15; // underweight
            if ($bmi < 25) return 25;   // normal weight
            if ($bmi < 30) return 35;   // overweight
            return 40;                  // obese
        }
    }
    
    public function getName(): string
    {
        return 'Katch-McArdle';
    }
    
    public function getDescription(): string
    {
        return 'La formule Katch-McArdle prend en compte la masse musculaire maigre pour un calcul plus précis pour les personnes sportives.';
    }
}