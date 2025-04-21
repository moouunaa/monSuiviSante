<?php

namespace App\Services;

use App\Builders\ProfileBuilderInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class ProfileService
{
    private $builder;
    
    public function __construct(ProfileBuilderInterface $builder)
    {
        $this->builder = $builder;
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
}