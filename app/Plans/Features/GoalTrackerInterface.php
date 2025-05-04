<?php

namespace App\Plans\Features;

interface GoalTrackerInterface
{
    public function trackGoal(array $data): array;
    public function getGoalProgress(int $userId): array;
    public function getMaxGoals(): int;
}