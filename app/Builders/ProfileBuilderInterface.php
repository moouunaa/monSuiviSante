<?php

namespace App\Builders;

interface ProfileBuilderInterface
{
    public function setBasicInfo(string $name): self;
    public function setPhysicalAttributes(string $gender, int $age, float $weight, int $height): self;
    public function setGoal(string $goal): self;
    public function setPlan(string $plan): self; 
    public function setAccount(string $email, string $password): self;
    public function calculateMetrics(): self;
    public function build(): array;
}