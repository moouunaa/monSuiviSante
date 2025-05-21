<?php

namespace App\Http\Controllers;

use App\Models\WaterEntry;
use App\Models\Goal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaterTrackingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        
        $waterEntries = WaterEntry::where('user_id', $user->id)
            ->where('entry_date', $today)
            ->orderBy('entry_time', 'desc')
            ->get();
            
        $totalWaterConsumed = $waterEntries->sum('amount');
        
        $waterGoal = Goal::where('user_id', $user->id)
            ->where('type', 'water')
            ->where('is_active', true)
            ->first();
            
        if (!$waterGoal) {
            $waterGoal = Goal::create([
                'user_id' => $user->id,
                'type' => 'water',
                'target_value' => 2000, // 2L en ml
                'is_active' => true,
            ]);
        }
        
        return view('water-tracking', [
            'user' => $user,
            'waterEntries' => $waterEntries,
            'totalWaterConsumed' => $totalWaterConsumed,
            'waterGoal' => $waterGoal
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1|max:2000',
            'entry_date' => 'required|date|before_or_equal:today',
            'entry_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500'
        ]);
        
        $entry = WaterEntry::create([
            'user_id' => Auth::id(),
            'amount' => $validated['amount'],
            'entry_date' => $validated['entry_date'],
            'entry_time' => $validated['entry_time'],
            'notes' => $validated['notes'] ?? null
        ]);
        
        return response()->json([
            'success' => true,
            'entry' => $entry
        ]);
    }
}