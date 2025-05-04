<?php

namespace App\Http\Controllers;

use App\Commands\AddFoodEntryCommand;
use App\Commands\DeleteFoodEntryCommand;
use App\Commands\CloneFoodEntryCommand;
use App\Models\FoodEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FoodTrackingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        
        $foodEntries = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', $today)
            ->orderBy('entry_time', 'asc')
            ->get();
            
        $recentEntries = FoodEntry::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return view('food-tracking', [
            'user' => $user,
            'foodEntries' => $foodEntries,
            'recentEntries' => $recentEntries
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'food_name' => 'required|string|max:255',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'calories' => 'required|integer|min:0',
            'portion_size' => 'required|string|max:50',
        ]);
        
        $command = new AddFoodEntryCommand(Auth::id(), $validated);
        $foodEntry = $command->execute();
        
        return response()->json([
            'success' => true,
            'entry' => $foodEntry
        ]);
    }
    
    public function destroy($id)
    {
        $command = new DeleteFoodEntryCommand($id, Auth::id());
        $command->execute();
        
        return response()->json([
            'success' => true
        ]);
    }
    
    public function clone($id)
    {
        $command = new CloneFoodEntryCommand($id, Auth::id());
        $newEntry = $command->execute();
        
        return response()->json([
            'success' => true,
            'entry' => $newEntry
        ]);
    }
}