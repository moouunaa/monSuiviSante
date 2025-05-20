<?php

namespace App\Builders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

class UserProfileBuilder implements ProfileBuilderInterface
{
    private array $userData = [];
    private array $profileData = [];
    
    public function setBasicInfo(string $name): self
    {
        $this->userData['name'] = $name;
        return $this;
    }
    
    public function setPhysicalAttributes(string $gender, int $age, float $weight, int $height): self
    {
        $this->profileData['gender'] = $gender;
        $this->profileData['age'] = $age;
        $this->profileData['weight'] = $weight;
        $this->profileData['height'] = $height;
        return $this;
    }
    
    public function setGoal(string $goal): self
    {
        $this->profileData['goal'] = $goal;
        return $this;
    }
    
    public function setPlan(string $plan): self
    {
        $this->profileData['plan'] = $plan;
        return $this;
    }
    
    public function setAccount(string $email, string $password): self
    {
        $this->userData['email'] = $email;
        $this->userData['password'] = Hash::make($password);
        return $this;
    }
    
    public function calculateMetrics(): self
    {
        // Calculer BMI
        $heightInMeters = $this->profileData['height'] / 100;
        $this->profileData['bmi'] = round($this->profileData['weight'] / ($heightInMeters * $heightInMeters), 1);
        
        // Calculate daily calorie target based on gender, weight, height, age and goal
        $bmr = $this->calculateBMR();
        
        // Adjust calories based on goal
        switch ($this->profileData['goal']) {
            case 'lose':
                $this->profileData['daily_calorie_target'] = round($bmr * 0.85); // 15% deficit
                break;
            case 'gain':
                $this->profileData['daily_calorie_target'] = round($bmr * 1.15); // 15% surplus
                break;
            default: // maintain
                $this->profileData['daily_calorie_target'] = round($bmr);
        }
        
        // Set macronutrient targets
        $this->calculateMacroTargets();
        
        // Set water target (ml) - basic formula based on weight
        $this->profileData['water_target'] = round($this->profileData['weight'] * 33); // 33ml per kg of body weight
        
        return $this;
    }
    
    private function calculateBMR(): float
    {
        // Mifflin-St Jeor Equation
        if ($this->profileData['gender'] === 'male') {
            return (10 * $this->profileData['weight']) + (6.25 * $this->profileData['height']) - (5 * $this->profileData['age']) + 5;
        } else {
            return (10 * $this->profileData['weight']) + (6.25 * $this->profileData['height']) - (5 * $this->profileData['age']) - 161;
        }
    }
    
    private function calculateMacroTargets(): void
    {
        $calories = $this->profileData['daily_calorie_target'];
    }
    
    public function build(): array
    {
        // Create the user
        $user = User::create($this->userData);
        
        // Add user_id to profile data
        $this->profileData['user_id'] = $user->id;
        
        // Create the profile
        $profile = UserProfile::create($this->profileData);
        
        return [
            'user' => $user,
            'profile' => $profile
        ];
    }
}