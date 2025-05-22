<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\Exercise;
use App\Models\CustomExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExerciseTrackingController extends Controller
{
    /**
     * Display the exercise tracking page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all workouts with pagination
        $workouts = Workout::where('user_id', $user->id)
            ->with('workoutItems.exercise')
            ->orderBy('entry_date', 'desc')
            ->orderBy('entry_time', 'desc')
            ->paginate(10);
            
        // Get exercises and custom exercises for dropdowns
        $exercises = Exercise::all();
        $customExercises = CustomExercise::where('user_id', $user->id)->get();
        
        // Calculate weekly stats
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $weeklyWorkouts = Workout::where('user_id', $user->id)
            ->whereBetween('entry_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();
            
        $totalWorkouts = $weeklyWorkouts->count();
        $totalDuration = $weeklyWorkouts->sum('total_duration');
        $totalCaloriesBurned = $weeklyWorkouts->sum('total_calories_burned');
        $avgDurationPerDay = $totalWorkouts > 0 ? round($totalDuration / 7) : 0;
        
        $weeklyStats = [
            'totalWorkouts' => $totalWorkouts,
            'totalDuration' => $totalDuration,
            'totalCaloriesBurned' => $totalCaloriesBurned,
            'avgDurationPerDay' => $avgDurationPerDay
        ];
        
        // Get daily calories burned for the chart
        $dailyCaloriesBurned = [];
        $dailyDuration = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $calories = Workout::where('user_id', $user->id)
                ->where('entry_date', $date)
                ->sum('total_calories_burned');
                
            $duration = Workout::where('user_id', $user->id)
                ->where('entry_date', $date)
                ->sum('total_duration');
                
            $dailyCaloriesBurned[$date] = $calories;
            $dailyDuration[$date] = $duration;
        }
        
        // Reverse to show oldest to newest
        $dailyCaloriesBurned = array_reverse($dailyCaloriesBurned);
        $dailyDuration = array_reverse($dailyDuration);
        
        return view('exercise-tracking', [
            'user' => $user,
            'workouts' => $workouts,
            'exercises' => $exercises,
            'customExercises' => $customExercises,
            'weeklyStats' => $weeklyStats,
            'dailyCaloriesBurned' => $dailyCaloriesBurned,
            'dailyDuration' => $dailyDuration
        ]);
    }
}
