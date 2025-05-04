<?php

namespace App\Plans\Features\Premium;

use App\Plans\Features\StatisticsInterface;

class PremiumStatistics implements StatisticsInterface
{
    public function generateStats(int $userId): array
    {
        // Statistiques avancées pour le plan premium
        return [
            'calories' => [
                'daily_average' => 2100,
                'weekly_trend' => -2,
                'breakdown' => [
                    'proteins' => 30,
                    'carbs' => 45,
                    'fats' => 25
                ]
            ],
            'weight' => [
                'current' => 75,
                'change' => -0.5,
                'trend' => 'downward',
                'projection' => 72 // poids projeté dans 4 semaines
            ],
            'activity' => [
                'daily_steps' => 8500,
                'active_minutes' => 45,
                'calories_burned' => 450
            ],
            'sleep' => [
                'average_duration' => 7.5,
                'quality_score' => 85
            ]
        ];
    }
    
    public function getAvailableCharts(): array
    {
        // Le plan premium a accès à tous les graphiques
        return [
            'weight', 'calories', 'macros', 'steps', 
            'sleep', 'water', 'body_measurements'
        ];
    }
    
    public function getHistoricalData(int $userId, string $metric, int $days): array
    {
        // Données historiques complètes pour le plan premium
        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $data[] = [
                'date' => date('Y-m-d', strtotime("-$i days")),
                'value' => rand(1800, 2200),
                'breakdown' => [
                    'breakfast' => rand(300, 500),
                    'lunch' => rand(500, 700),
                    'dinner' => rand(500, 700),
                    'snacks' => rand(200, 400)
                ]
            ];
        }
        
        return $data;
    }
}