<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\FoodEntry;
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
        
        // Get today's food entries
        $todayEntries = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', $today)
            ->orderBy('entry_time', 'asc')
            ->get();
            
        // Calculate total calories consumed today
        $caloriesConsumed = $todayEntries->sum('calories');
        
        return view('dashboard', [
            'user' => $user,
            'calorieGoal' => $calorieGoal,
            'waterGoal' => $waterGoal,
            'sleepGoal' => $sleepGoal,
            'caloriesConsumed' => $caloriesConsumed,
            'todayMeals' => $todayEntries,
        ]);
    }
}