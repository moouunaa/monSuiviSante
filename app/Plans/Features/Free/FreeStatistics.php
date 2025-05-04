<?php

namespace App\Plans\Features\Free;

use App\Plans\Features\StatisticsInterface;

class FreeStatistics implements StatisticsInterface
{
    public function generateStats(int $userId): array
    {
        // Statistiques basiques pour le plan gratuit
        return [
            'calories' => [
                'daily_average' => 2100,
                'weekly_trend' => -2 // pourcentage de changement
            ],
            'weight' => [
                'current' => 75,
                'change' => -0.5
            ]
        ];
    }
    
    public function getAvailableCharts(): array
    {
        // Le plan gratuit n'a accès qu'à des graphiques limités
        return ['weight', 'calories'];
    }
    
    public function getHistoricalData(int $userId, string $metric, int $days): array
    {
        // Limite les données historiques à 7 jours pour le plan gratuit
        $days = min($days, 7);
        
        // Simuler des données pour l'exemple
        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $data[] = [
                'date' => date('Y-m-d', strtotime("-$i days")),
                'value' => rand(1800, 2200) // valeurs aléatoires pour l'exemple
            ];
        }
        
        return $data;
    }
}