<?php

namespace App\Plans\Features;

interface MealPlannerInterface
{
    public function generateMealPlan(int $userId): array;
    public function getSuggestedMeals(int $userId): array;
    public function getCustomizationOptions(): array;
}