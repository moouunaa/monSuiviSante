<?php

namespace App\Services;

use App\Plans\PlanFactoryInterface;
use App\Plans\FreePlanFactory;
use App\Plans\PremiumPlanFactory;

class PlanService
{
    public function getPlanFactory(string $planType): PlanFactoryInterface
    {
        switch ($planType) {
            case 'premium':
                return new PremiumPlanFactory();
            case 'free':
            default:
                return new FreePlanFactory();
        }
    }
}