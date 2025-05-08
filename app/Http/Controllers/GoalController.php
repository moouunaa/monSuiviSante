<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\UserProfile;
use App\Services\CalorieCalculator;
use App\Strategies\MifflinStJeorStrategy;
use App\Strategies\HarrisBenedictStrategy;
use App\Strategies\KatchMcArdleStrategy;
use App\Strategies\CustomCalorieStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'calculation_method' => 'required|in:mifflin_st_jeor,harris_benedict,katch_mcardle,custom',
            'custom_value' => 'required_if:calculation_method,custom|nullable|integer|min:1000|max:5000',
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
        
        // Set default water goal if it doesn't exist
        Goal::firstOrCreate(
            [
                'user_id' => $user->id,
                'type' => 'water',
            ],
            [
                'target_value' => 2000, // 2L in ml
                'is_active' => true,
            ]
        );
        
        // Set default sleep goal if it doesn't exist
        Goal::firstOrCreate(
            [
                'user_id' => $user->id,
                'type' => 'sleep',
            ],
            [
                'target_value' => 480, // 8 hours in minutes
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