<?php

namespace App\Plans\Features\Free;

use App\Plans\Features\GoalTrackerInterface;

class FreeGoalTracker implements GoalTrackerInterface
{
    public function trackGoal(array $data): array
    {
        // Implémentation basique pour le plan gratuit
        return [
            'status' => 'success',
            'message' => 'Goal tracked successfully',
            'data' => $data
        ];
    }
    
    public function getGoalProgress(int $userId): array
    {
        // Retourne des données de progression basiques
        return [
            'completed' => 2,
            'total' => 3,
            'percentage' => 66
        ];
    }
    
    public function getMaxGoals(): int
    {
        // Le plan gratuit permet de suivre jusqu'à 3 objectifs
        return 3;
    }
}