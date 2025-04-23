<?php

namespace App\Services;

use App\Builders\ProfileBuilderInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class ProfileService
{
    private $builder;
    private $planService;
    
    public function __construct(ProfileBuilderInterface $builder, PlanService $planService = null)
    {
        $this->builder = $builder;
        $this->planService = $planService ?? new PlanService();
    }
    
    public function createUserProfile(array $data): array
    {
        DB::beginTransaction();
        
        try {
            // Build the user profile step by step
            $result = $this->builder
                ->setBasicInfo($data['name'])
                ->setPhysicalAttributes($data['gender'], $data['age'], $data['weight'], $data['height'])
                ->setGoal($data['goal'])
                ->setPlan($data['plan'])
                ->setAccount($data['email'], $data['password'])
                ->calculateMetrics()
                ->build();
                
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function getPlanFeatures(string $planType)
    {
        $factory = $this->planService->getPlanFactory($planType);
        
        return [
            'goalTracker' => $factory->createGoalTracker(),
            'statistics' => $factory->createStatistics(),
            'mealPlanner' => $factory->createMealPlanner()
        ];
    }
}