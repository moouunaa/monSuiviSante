<?php

namespace App\Plans\Features\Free;

use App\Plans\Features\MealPlannerInterface;

class FreeMealPlanner implements MealPlannerInterface
{
    public function generateMealPlan(int $userId): array
    {
        // Plan de repas basique pour le plan gratuit
        return [
            'breakfast' => ['Oatmeal with fruits', 'Scrambled eggs'],
            'lunch' => ['Chicken salad', 'Vegetable soup'],
            'dinner' => ['Grilled fish', 'Steamed vegetables']
        ];
    }
    
    public function getSuggestedMeals(int $userId): array
    {
        // Suggestions limitées pour le plan gratuit
        return [
            'Oatmeal with fruits',
            'Chicken salad',
            'Grilled fish'
        ];
    }
    
    public function getCustomizationOptions(): array
    {
        // Options de personnalisation limitées
        return [
            'exclude_ingredients' => true,
            'dietary_preferences' => ['vegetarian', 'vegan']
        ];
    }
}