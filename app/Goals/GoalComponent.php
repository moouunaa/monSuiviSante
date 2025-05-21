<?php

namespace App\Goals;

abstract class GoalComponent
{
    abstract public function getProgress(): float;
    abstract public function getTarget(): float;
    abstract public function getCurrentValue(): float;
    abstract public function getType(): string;
    abstract public function getName(): string;
    abstract public function getIcon(): string;
    abstract public function getUnit(): string;
}