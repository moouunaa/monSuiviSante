<?php

namespace App\Goals;

class GoalGroup extends GoalComponent
{
    protected string $name;
    protected string $icon;
    protected string $type;
    protected array $goals = [];

    public function __construct(string $name, string $icon, string $type)
    {
        $this->name = $name;
        $this->icon = $icon;
        $this->type = $type;
    }

    public function addGoal(GoalComponent $goal): void
    {
        $this->goals[] = $goal;
    }

    public function getGoals(): array
    {
        return $this->goals;
    }

    public function getProgress(): float
    {
        if (empty($this->goals)) {
            return 0;
        }
        
        $totalProgress = 0;
        foreach ($this->goals as $goal) {
            $totalProgress += $goal->getProgress();
        }
        
        return $totalProgress / count($this->goals);
    }

    public function getTarget(): float
    {
        $totalTarget = 0;
        foreach ($this->goals as $goal) {
            $totalTarget += $goal->getTarget();
        }
        
        return $totalTarget;
    }

    public function getCurrentValue(): float
    {
        $totalValue = 0;
        foreach ($this->goals as $goal) {
            $totalValue += $goal->getCurrentValue();
        }
        
        return $totalValue;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getUnit(): string
    {
        return '';
    }
}