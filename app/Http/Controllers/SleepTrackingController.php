<?php

namespace App\Http\Controllers;

use App\Models\SleepEntry;
use App\Models\Goal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SleepTrackingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        
        $sleepEntries = SleepEntry::where('user_id', $user->id)
            ->orderBy('sleep_date', 'desc')
            ->take(10)
            ->get();
            
        $lastNight = SleepEntry::where('user_id', $user->id)
            ->where('sleep_date', Carbon::yesterday()->format('Y-m-d'))
            ->first();
            
        $sleepGoal = Goal::where('user_id', $user->id)
            ->where('type', 'sleep')
            ->where('is_active', true)
            ->first();
            
        if (!$sleepGoal) {
            $sleepGoal = Goal::create([
                'user_id' => $user->id,
                'type' => 'sleep',
                'target_value' => 480, // 8 heures en minutes
                'is_active' => true,
            ]);
        }
        
        return view('sleep-tracking', [
            'user' => $user,
            'sleepEntries' => $sleepEntries,
            'lastNight' => $lastNight,
            'sleepGoal' => $sleepGoal
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sleep_date' => 'required|date|before_or_equal:today',
            'sleep_time' => 'required|date_format:H:i',
            'wake_time' => 'required|date_format:H:i',
            'quality' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string|max:500'
        ]);
        
        // Calculer la durée du sommeil en minutes
        $sleepTime = Carbon::createFromFormat('H:i', $validated['sleep_time']);
        $wakeTime = Carbon::createFromFormat('H:i', $validated['wake_time']);
        
        // Si l'heure de réveil est avant l'heure de coucher, on ajoute 1 jour
        if ($wakeTime < $sleepTime) {
            $wakeTime->addDay();
        }
        
        $durationInMinutes = $wakeTime->diffInMinutes($sleepTime);
        
        $entry = SleepEntry::create([
            'user_id' => Auth::id(),
            'sleep_date' => $validated['sleep_date'],
            'sleep_time' => $validated['sleep_time'],
            'wake_time' => $validated['wake_time'],
            'duration' => $durationInMinutes,
            'quality' => $validated['quality'],
            'notes' => $validated['notes'] ?? null
        ]);
        
        return response()->json([
            'success' => true,
            'entry' => $entry
        ]);
    }
}