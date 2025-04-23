<?php

namespace App\Plans;

use App\Plans\Features\GoalTrackerInterface;
use App\Plans\Features\StatisticsInterface;
use App\Plans\Features\MealPlannerInterface;
use App\Plans\Features\Free\FreeGoalTracker;
use App\Plans\Features\Free\FreeStatistics;
use App\Plans\Features\Free\FreeMealPlanner;

class FreePlanFactory implements PlanFactoryInterface
{
    public function createGoalTracker(): GoalTrackerInterface
    {
        return new FreeGoalTracker();
    }
    
    public function createStatistics(): StatisticsInterface
    {
        return new FreeStatistics();
    }
    
    public function createMealPlanner(): MealPlannerInterface
    {
        return new FreeMealPlanner();
    }
}