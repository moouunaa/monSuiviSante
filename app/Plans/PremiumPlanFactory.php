<?php

namespace App\Plans;

use App\Plans\Features\GoalTrackerInterface;
use App\Plans\Features\StatisticsInterface;
use App\Plans\Features\MealPlannerInterface;
use App\Plans\Features\Premium\PremiumGoalTracker;
use App\Plans\Features\Premium\PremiumStatistics;
use App\Plans\Features\Premium\PremiumMealPlanner;

class PremiumPlanFactory implements PlanFactoryInterface
{
    public function createGoalTracker(): GoalTrackerInterface
    {
        return new PremiumGoalTracker();
    }
    
    public function createStatistics(): StatisticsInterface
    {
        return new PremiumStatistics();
    }
    
    public function createMealPlanner(): MealPlannerInterface
    {
        return new PremiumMealPlanner();
    }
}