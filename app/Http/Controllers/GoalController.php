<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\UserProfile;
use App\Models\WeightEntry;
use App\Services\CalorieCalculator;
use App\Strategies\MifflinStJeorStrategy;
use App\Strategies\HarrisBenedictStrategy;
use App\Strategies\KatchMcArdleStrategy;
use App\Strategies\CustomCalorieStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all user goals
        $calorieGoal = Goal::where('user_id', $user->id)
            ->where('type', 'calories')
            ->where('is_active', true)
            ->first();
            
        $waterGoal = Goal::where('user_id', $user->id)
            ->where('type', 'water')
            ->where('is_active', true)
            ->first();
            
        $sleepGoal = Goal::where('user_id', $user->id)
            ->where('type', 'sleep')
            ->where('is_active', true)
            ->first();
            
        $weightGoal = Goal::where('user_id', $user->id)
            ->where('type', 'weight')
            ->where('is_active', true)
            ->first();
            
        // Get latest weight entry
        $latestWeight = WeightEntry::where('user_id', $user->id)
            ->orderBy('entry_date', 'desc')
            ->first();
            
        // Get available calculation methods
        $userProfile = $user->profile;
        $calculationMethods = [];
        
        if ($userProfile) {
            $methods = [
                'mifflin_st_jeor' => new MifflinStJeorStrategy(),
                'harris_benedict' => new HarrisBenedictStrategy(),
                'katch_mcardle' => new KatchMcArdleStrategy(),
            ];
            
            $calculator = new CalorieCalculator();
            
            foreach ($methods as $key => $strategy) {
                $calculator->setStrategy($strategy);
                $calculationMethods[] = [
                    'id' => $key,
                    'name' => $strategy->getName(),
                    'description' => $strategy->getDescription(),
                    'calories' => $calculator->calculateDailyCalories($userProfile),
                ];
            }
        }
        
        return view('goals', [
            'user' => $user,
            'calorieGoal' => $calorieGoal,
            'waterGoal' => $waterGoal,
            'sleepGoal' => $sleepGoal,
            'weightGoal' => $weightGoal,
            'latestWeight' => $latestWeight,
            'calculationMethods' => $calculationMethods
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'calculation_method' => 'required|in:mifflin_st_jeor,harris_benedict,katch_mcardle,custom',
            'custom_value' => 'required_if:calculation_method,custom|nullable|integer|min:1000|max:5000',
            'weight_goal_type' => 'nullable|in:lose,maintain,gain',
        ]);
        
        $user = Auth::user();
        $userProfile = $user->profile;
        
        if (!$userProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil utilisateur non trouvé'
            ], 404);
        }
        
        // Calculate calories based on selected method
        $calculator = new CalorieCalculator();
        
        switch ($validated['calculation_method']) {
            case 'harris_benedict':
                $calculator->setStrategy(new HarrisBenedictStrategy());
                break;
            case 'katch_mcardle':
                $calculator->setStrategy(new KatchMcArdleStrategy());
                break;
            case 'custom':
                $customStrategy = new CustomCalorieStrategy($validated['custom_value']);
                $calculator->setStrategy($customStrategy);
                break;
            case 'mifflin_st_jeor':
            default:
                $calculator->setStrategy(new MifflinStJeorStrategy());
                break;
        }
        
        $calories = $calculator->calculateDailyCalories($userProfile);
        
        // Adjust calories based on weight goal type if provided
        if (isset($validated['weight_goal_type'])) {
            switch ($validated['weight_goal_type']) {
                case 'lose':
                    $calories -= 500; // Deficit for weight loss
                    break;
                case 'gain':
                    $calories += 500; // Surplus for weight gain
                    break;
                // 'maintain' doesn't need adjustment
            }
            
            // Update user profile goal
            $userProfile->update([
                'goal' => $validated['weight_goal_type']
            ]);
        }
        
        // Update or create calorie goal
        Goal::updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => 'calories',
            ],
            [
                'target_value' => $validated['calculation_method'] === 'custom' ? $validated['custom_value'] : $calories,
                'calculation_method' => $validated['calculation_method'],
                'custom_value' => $validated['calculation_method'] === 'custom' ? $validated['custom_value'] : null,
                'is_active' => true,
            ]
        );
        
        return response()->json([
            'success' => true,
            'calories' => $calories,
            'method' => $validated['calculation_method'],
            'methodName' => $calculator->getStrategyName(),
        ]);
    }
    
    public function updateWaterGoal(Request $request)
    {
        $validated = $request->validate([
            'target_value' => 'required|integer|min:500|max:5000',
        ]);
        
        $user = Auth::user();
        
        Goal::updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => 'water',
            ],
            [
                'target_value' => $validated['target_value'],
                'is_active' => true,
            ]
        );
        
        return response()->json([
            'success' => true,
        ]);
    }
    
    public function updateSleepGoal(Request $request)
    {
        $validated = $request->validate([
            'hours' => 'required|integer|min:4|max:12',
            'minutes' => 'required|integer|min:0|max:59',
        ]);
        
        $user = Auth::user();
        $totalMinutes = ($validated['hours'] * 60) + $validated['minutes'];
        
        Goal::updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => 'sleep',
            ],
            [
                'target_value' => $totalMinutes,
                'is_active' => true,
            ]
        );
        
        return response()->json([
            'success' => true,
        ]);
    }
    
    public function updateWeightGoal(Request $request)
    {
        $validated = $request->validate([
            'target_weight' => 'required|numeric|min:20|max:500',
            'target_date' => 'required|date|after:today',
        ]);
        
        $user = Auth::user();
        
        Goal::updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => 'weight',
            ],
            [
                'target_value' => $validated['target_weight'],
                'target_date' => $validated['target_date'],
                'is_active' => true,
            ]
        );
        
        return response()->json([
            'success' => true,
        ]);
    }
    
    public function getCalorieGoal()
    {
        $user = Auth::user();
        $calorieGoal = Goal::where('user_id', $user->id)
            ->where('type', 'calories')
            ->where('is_active', true)
            ->first();
            
        if (!$calorieGoal) {
            // Calculate default goal using Mifflin-St Jeor
            $userProfile = $user->profile;
            
            if (!$userProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil utilisateur non trouvé'
                ], 404);
            }
            
            $calculator = new CalorieCalculator(new MifflinStJeorStrategy());
            $calories = $calculator->calculateDailyCalories($userProfile);
            
            $calorieGoal = Goal::create([
                'user_id' => $user->id,
                'type' => 'calories',
                'target_value' => $calories,
                'calculation_method' => 'mifflin_st_jeor',
                'is_active' => true,
            ]);
        }
        
        // Get available calculation methods
        $userProfile = $user->profile;
        $calculationMethods = [];
        
        if ($userProfile) {
            $methods = [
                'mifflin_st_jeor' => new MifflinStJeorStrategy(),
                'harris_benedict' => new HarrisBenedictStrategy(),
                'katch_mcardle' => new KatchMcArdleStrategy(),
            ];
            
            $calculator = new CalorieCalculator();
            
            foreach ($methods as $key => $strategy) {
                $calculator->setStrategy($strategy);
                $calculationMethods[] = [
                    'id' => $key,
                    'name' => $strategy->getName(),
                    'description' => $strategy->getDescription(),
                    'calories' => $calculator->calculateDailyCalories($userProfile),
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'goal' => $calorieGoal,
            'calculationMethods' => $calculationMethods,
        ]);
    }
}