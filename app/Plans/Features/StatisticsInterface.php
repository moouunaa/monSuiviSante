<?php

namespace App\Plans\Features;

interface StatisticsInterface
{
    public function generateStats(int $userId): array;
    public function getAvailableCharts(): array;
    public function getHistoricalData(int $userId, string $metric, int $days): array;
}