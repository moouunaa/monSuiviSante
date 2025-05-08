<?php

namespace App\Strategies;

use App\Models\UserProfile;

class CustomCalorieStrategy implements CalorieCalculationStrategy
{
    private $calorieValue;
    
    public function __construct(int $calorieValue = 2000)
    {
        $this->calorieValue = $calorieValue;
    }
    
    public function calculateCalories(UserProfile $userProfile): int
    {
        return $this->calorieValue;
    }
    
    public function setCalorieValue(int $value): void
    {
        $this->calorieValue = $value;
    }
    
    public function getName(): string
    {
        return 'Personnalisé';
    }
    
    public function getDescription(): string
    {
        return 'Définissez manuellement votre objectif calorique quotidien.';
    }
}