<?php

namespace App\Goals;

use App\Models\Goal;

class IndividualGoal extends GoalComponent
{
    protected Goal $goal;
    protected float $currentValue;
    protected string $name;
    protected string $icon;
    protected string $unit;

    public function __construct(Goal $goal, float $currentValue, string $name, string $icon, string $unit)
    {
        $this->goal = $goal;
        $this->currentValue = $currentValue;
        $this->name = $name;
        $this->icon = $icon;
        $this->unit = $unit;
    }

    public function getProgress(): float
    {
        if ($this->goal->target_value <= 0) {
            return 0;
        }
        
        return min(100, ($this->currentValue / $this->goal->target_value) * 100);
    }

    public function getTarget(): float
    {
        return $this->goal->target_value;
    }

    public function getCurrentValue(): float
    {
        return $this->currentValue;
    }

    public function getType(): string
    {
        return $this->goal->type;
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
        return $this->unit;
    }
}