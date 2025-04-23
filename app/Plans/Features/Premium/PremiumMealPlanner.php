<?php

namespace App\Plans\Features\Premium;

use App\Plans\Features\MealPlannerInterface;

class PremiumMealPlanner implements MealPlannerInterface
{
    public function generateMealPlan(int $userId): array
    {
        // Plan de repas avancé pour le plan premium
        return [
            'breakfast' => [
                [
                    'name' => 'Protein Oatmeal',
                    'calories' => 350,
                    'protein' => 20,
                    'carbs' => 45,
                    'fat' => 10,
                    'recipe' => 'Mix oats with protein powder...'
                ],
                [
                    'name' => 'Avocado Toast with Eggs',
                    'calories' => 420,
                    'protein' => 18,
                    'carbs' => 30,
                    'fat' => 25,
                    'recipe' => 'Toast bread, mash avocado...'
                ]
            ],
            'lunch' => [
                // Détails similaires pour le déjeuner
            ],
            'dinner' => [
                // Détails similaires pour le dîner
            ],
            'snacks' => [
                // Collations suggérées
            ]
        ];
    }
    
    public function getSuggestedMeals(int $userId): array
    {
        // Suggestions personnalisées pour le plan premium
        return [
            [
                'name' => 'Protein Oatmeal',
                'calories' => 350,
                'protein' => 20,
                'matches_goals' => true,
                'rating' => 4.5
            ],
            // Plus de suggestions...
        ];
    }
    
    public function getCustomizationOptions(): array
    {
        // Options de personnalisation avancées
        return [
            'exclude_ingredients' => true,
            'dietary_preferences' => [
                'vegetarian', 'vegan', 'keto', 'paleo', 
                'gluten_free', 'dairy_free', 'low_carb'
            ],
            'meal_count' => true,
            'calorie_range' => true,
            'macro_targets' => true,
            'cooking_time' => true,
            'difficulty_level' => true
        ];
    }
}