<?php

namespace App\Plans;

use App\Plans\Features\GoalTrackerInterface;
use App\Plans\Features\StatisticsInterface;
use App\Plans\Features\MealPlannerInterface;

interface PlanFactoryInterface
{
    public function createGoalTracker(): GoalTrackerInterface;
    public function createStatistics(): StatisticsInterface;
    public function createMealPlanner(): MealPlannerInterface;
}