<?php

namespace App\Http\Controllers;

use App\Goals\GoalGroup;
use App\Goals\IndividualGoal;
use App\Models\Goal;
use App\Models\FoodEntry;
use App\Models\ExerciseEntry;
use App\Models\WaterEntry;
use App\Models\SleepEntry;
use App\Models\WeightEntry;
use App\Services\CalorieCalculator;
use App\Strategies\MifflinStJeorStrategy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        
        // Get user's goals
        $calorieGoal = Goal::where('user_id', $user->id)
            ->where('type', 'calories')
            ->where('is_active', true)
            ->first();
            
        // If no calorie goal exists, calculate default
        if (!$calorieGoal && $user->profile) {
            $calculator = new CalorieCalculator(new MifflinStJeorStrategy());
            $calories = $calculator->calculateDailyCalories($user->profile);
            
            $calorieGoal = Goal::create([
                'user_id' => $user->id,
                'type' => 'calories',
                'target_value' => $calories,
                'calculation_method' => 'mifflin_st_jeor',
                'is_active' => true,
            ]);
        }
        
        // Get water and sleep goals
        $waterGoal = Goal::where('user_id', $user->id)
            ->where('type', 'water')
            ->where('is_active', true)
            ->first();
            
        if (!$waterGoal) {
            $waterGoal = Goal::create([
                'user_id' => $user->id,
                'type' => 'water',
                'target_value' => 2000, // 2L in ml
                'is_active' => true,
            ]);
        }
        
        $sleepGoal = Goal::where('user_id', $user->id)
            ->where('type', 'sleep')
            ->where('is_active', true)
            ->first();
            
        if (!$sleepGoal) {
            $sleepGoal = Goal::create([
                'user_id' => $user->id,
                'type' => 'sleep',
                'target_value' => 480, // 8 hours in minutes
                'is_active' => true,
            ]);
        }
        
        // Get weight goal
        $weightGoal = Goal::where('user_id', $user->id)
            ->where('type', 'weight')
            ->where('is_active', true)
            ->first();
            
        // Get today's food entries
        $todayEntries = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', $today)
            ->orderBy('entry_time', 'asc')
            ->get();
            
        // Calculate total calories consumed today
        $caloriesConsumed = $todayEntries->sum('calories');
        
        // Get today's exercise entries and calculate calories burned
        $todayExercises = ExerciseEntry::where('user_id', $user->id)
            ->where('entry_date', $today)
            ->get();
            
        $caloriesBurned = $todayExercises->sum('calories_burned');
        
        // Get today's water entries
        $todayWater = WaterEntry::where('user_id', $user->id)
            ->where('entry_date', $today)
            ->get();
            
        $waterConsumed = $todayWater->sum('amount');
        
        // Get last night's sleep
        $lastNight = SleepEntry::where('user_id', $user->id)
            ->where('sleep_date', Carbon::yesterday()->format('Y-m-d'))
            ->first();
            
        $sleepDuration = $lastNight ? $lastNight->duration : 0;
        
        // Calculate remaining calories
        $caloriesRemaining = ($calorieGoal->target_value ?? 2000) - $caloriesConsumed + $caloriesBurned;
        
        // Get recent weight entries for the graph
        $weightEntries = WeightEntry::where('user_id', $user->id)
            ->orderBy('entry_date', 'desc')
            ->take(10)
            ->get()
            ->sortBy('entry_date');
            
        // Create goal components using the Composite Pattern
        $calorieGoalComponent = new IndividualGoal(
            $calorieGoal ?? new Goal(['target_value' => 2000]),
            $caloriesConsumed,
            'Calories',
            '🔥',
            'kcal'
        );
        
        $waterGoalComponent = new IndividualGoal(
            $waterGoal ?? new Goal(['target_value' => 2000]),
            $waterConsumed,
            'Hydratation',
            '💧',
            'ml'
        );
        
        $sleepGoalComponent = new IndividualGoal(
            $sleepGoal ?? new Goal(['target_value' => 480]),
            $sleepDuration,
            'Sommeil',
            '😴',
            'heures'
        );
        
        // Create a goal group for daily goals
        $dailyGoals = new GoalGroup('Objectifs quotidiens', '📊', 'daily');
        $dailyGoals->addGoal($calorieGoalComponent);
        $dailyGoals->addGoal($waterGoalComponent);
        $dailyGoals->addGoal($sleepGoalComponent);
        
        return view('dashboard', [
            'user' => $user,
            'calorieGoal' => $calorieGoal,
            'waterGoal' => $waterGoal,
            'sleepGoal' => $sleepGoal,
            'weightGoal' => $weightGoal,
            'caloriesConsumed' => $caloriesConsumed,
            'caloriesBurned' => $caloriesBurned,
            'caloriesRemaining' => $caloriesRemaining,
            'waterConsumed' => $waterConsumed,
            'sleepDuration' => $sleepDuration,
            'todayMeals' => $todayEntries,
            'weightEntries' => $weightEntries,
            'dailyGoals' => $dailyGoals
        ]);
    }
}