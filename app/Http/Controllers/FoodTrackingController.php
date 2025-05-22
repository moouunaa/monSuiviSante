<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Food;
use App\Models\CustomFood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FoodTrackingController extends Controller
{
    /**
     * Display the food tracking page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all meals with pagination
        $meals = Meal::where('user_id', $user->id)
            ->with('mealItems.food')
            ->orderBy('entry_date', 'desc')
            ->orderBy('entry_time', 'desc')
            ->paginate(10);
            
        // Get foods and custom foods for dropdowns
        $foods = Food::all();
        $customFoods = CustomFood::where('user_id', $user->id)->get();
        
        // Calculate weekly stats
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $weeklyMeals = Meal::where('user_id', $user->id)
            ->whereBetween('entry_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();
            
        $totalMeals = $weeklyMeals->count();
        $totalCalories = $weeklyMeals->sum('total_calories');
        $avgCalories = $totalMeals > 0 ? round($totalCalories / 7) : 0;
        $avgMealsPerDay = $totalMeals > 0 ? round($totalMeals / 7, 1) : 0;
        
        // Get most common meal type
        $mealTypes = $weeklyMeals->groupBy('meal_type')->map->count();
        $mostCommonMealType = $mealTypes->count() > 0 ? ucfirst($mealTypes->sortDesc()->keys()->first()) : 'N/A';
        
        $weeklyStats = [
            'totalMeals' => $totalMeals,
            'avgCalories' => $avgCalories,
            'avgMealsPerDay' => $avgMealsPerDay,
            'mostCommonMealType' => $mostCommonMealType
        ];
        
        // Get daily calories for the chart
        $dailyCalories = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $calories = Meal::where('user_id', $user->id)
                ->where('entry_date', $date)
                ->sum('total_calories');
                
            $dailyCalories[$date] = $calories;
        }
        
        // Reverse to show oldest to newest
        $dailyCalories = array_reverse($dailyCalories);
        
        return view('food-tracking', [
            'user' => $user,
            'meals' => $meals,
            'foods' => $foods,
            'customFoods' => $customFoods,
            'weeklyStats' => $weeklyStats,
            'dailyCalories' => $dailyCalories
        ]);
    }
}
