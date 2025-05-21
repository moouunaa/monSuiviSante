<?php

namespace App\Http\Controllers;

use App\Models\WeightEntry;
use App\Models\Goal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeightTrackingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get weight entries for the last 30 days for the chart
        $chartEntries = WeightEntry::where('user_id', $user->id)
            ->where('entry_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('entry_date', 'asc')
            ->get();
            
        // Get paginated entries for the history table
        $weightEntries = WeightEntry::where('user_id', $user->id)
            ->orderBy('entry_date', 'desc')
            ->paginate(20);
            
        $weightGoal = Goal::where('user_id', $user->id)
            ->where('type', 'weight')
            ->where('is_active', true)
            ->first();
            
        $latestWeight = WeightEntry::where('user_id', $user->id)
            ->orderBy('entry_date', 'desc')
            ->first();
            
        return view('weight-tracking', [
            'user' => $user,
            'weightEntries' => $weightEntries,
            'chartEntries' => $chartEntries,
            'weightGoal' => $weightGoal,
            'latestWeight' => $latestWeight
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'weight' => 'required|numeric|min:20|max:500',
            'entry_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500'
        ]);
        
        $entry = WeightEntry::create([
            'user_id' => Auth::id(),
            'weight' => $validated['weight'],
            'entry_date' => $validated['entry_date'],
            'notes' => $validated['notes'] ?? null
        ]);
        
        return response()->json([
            'success' => true,
            'entry' => $entry
        ]);
    }
}