<?php

namespace App\Http\Controllers;

use App\Models\ExerciseEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExerciseTrackingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        
        $exerciseEntries = ExerciseEntry::where('user_id', $user->id)
            ->where('entry_date', $today)
            ->orderBy('entry_time', 'desc')
            ->get();
            
        $totalCaloriesBurned = $exerciseEntries->sum('calories_burned');
        
        return view('exercise-tracking', [
            'user' => $user,
            'exerciseEntries' => $exerciseEntries,
            'totalCaloriesBurned' => $totalCaloriesBurned
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exercise_name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'calories_burned' => 'required|integer|min:1',
            'entry_date' => 'required|date|before_or_equal:today',
            'entry_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500'
        ]);
        
        $entry = ExerciseEntry::create([
            'user_id' => Auth::id(),
            'exercise_name' => $validated['exercise_name'],
            'duration' => $validated['duration'],
            'calories_burned' => $validated['calories_burned'],
            'entry_date' => $validated['entry_date'],
            'entry_time' => $validated['entry_time'],
            'notes' => $validated['notes'] ?? null
        ]);
        
        return response()->json([
            'success' => true,
            'entry' => $entry
        ]);
    }
    
    public function destroy($id)
    {
        $entry = ExerciseEntry::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        $entry->delete();
        
        return response()->json([
            'success' => true
        ]);
    }
}