<?php

namespace App\Strategies;

use App\Models\UserProfile;

interface CalorieCalculationStrategy
{
    public function calculateCalories(UserProfile $userProfile): int;
    public function getName(): string;
    public function getDescription(): string;
}