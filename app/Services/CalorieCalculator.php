<?php

namespace App\Services;

use App\Models\UserProfile;
use App\Strategies\CalorieCalculationStrategy;
use App\Strategies\MifflinStJeorStrategy;

class CalorieCalculator
{
    private $strategy;
    
    public function __construct(CalorieCalculationStrategy $strategy = null)
    {
        $this->strategy = $strategy ?? new MifflinStJeorStrategy();
    }
    
    public function setStrategy(CalorieCalculationStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }
    
    public function calculateDailyCalories(UserProfile $userProfile): int
    {
        return $this->strategy->calculateCalories($userProfile);
    }
    
    public function getStrategyName(): string
    {
        return $this->strategy->getName();
    }
    
    public function getStrategyDescription(): string
    {
        return $this->strategy->getDescription();
    }
}