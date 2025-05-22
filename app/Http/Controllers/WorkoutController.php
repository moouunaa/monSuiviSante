<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\WorkoutItem;
use App\Models\Exercise;
use App\Models\CustomExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $workouts = Workout::where('user_id', Auth::id())
            ->with('workoutItems.exercise')
            ->orderBy('entry_date', 'desc')
            ->orderBy('entry_time', 'desc')
            ->paginate(15);
            
        return view('workouts.index', compact('workouts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'entry_time' => 'required',
            'notes' => 'nullable|string',
            'workout_items' => 'required|array|min:1',
            'workout_items.*.exercise_type' => 'required|string',
            'workout_items.*.exercise_id' => 'required|integer',
            'workout_items.*.duration' => 'required|integer|min:1',
            'workout_items.*.sets' => 'nullable|integer|min:1',
            'workout_items.*.reps' => 'nullable|integer|min:1',
            'workout_items.*.calories_burned' => 'required|integer|min:0',
            'total_duration' => 'required|integer|min:1',
            'total_calories_burned' => 'required|integer|min:0',
        ]);
        
        // Create the workout
        $workout = new Workout();
        $workout->user_id = Auth::id();
        $workout->entry_date = $validated['entry_date'];
        $workout->entry_time = $validated['entry_time'];
        $workout->total_duration = $validated['total_duration'];
        $workout->total_calories_burned = $validated['total_calories_burned'];
        $workout->notes = $validated['notes'];
        $workout->save();
        
        // Add workout items
        foreach ($validated['workout_items'] as $itemData) {
            $workoutItem = new WorkoutItem();
            $workoutItem->workout_id = $workout->id;
            $workoutItem->exercise_type = $itemData['exercise_type'];
            $workoutItem->exercise_id = $itemData['exercise_id'];
            $workoutItem->duration = $itemData['duration'];
            $workoutItem->sets = $itemData['sets'];
            $workoutItem->reps = $itemData['reps'];
            $workoutItem->calories_burned = $itemData['calories_burned'];
            $workoutItem->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Entraînement enregistré avec succès',
            'workout' => $workout->load('workoutItems')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Workout  $workout
     * @return \Illuminate\Http\Response
     */
    public function show(Workout $workout)
    {
        if ($workout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $workout->load('workoutItems.exercise');
        
        return view('workouts.show', compact('workout'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Workout  $workout
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Workout $workout)
    {
        if ($workout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'entry_time' => 'required',
            'notes' => 'nullable|string',
            'workout_items' => 'required|array|min:1',
            'workout_items.*.exercise_type' => 'required|string',
            'workout_items.*.exercise_id' => 'required|integer',
            'workout_items.*.duration' => 'required|integer|min:1',
            'workout_items.*.sets' => 'nullable|integer|min:1',
            'workout_items.*.reps' => 'nullable|integer|min:1',
            'workout_items.*.calories_burned' => 'required|integer|min:0',
            'total_duration' => 'required|integer|min:1',
            'total_calories_burned' => 'required|integer|min:0',
        ]);
        
        // Update the workout
        $workout->entry_date = $validated['entry_date'];
        $workout->entry_time = $validated['entry_time'];
        $workout->total_duration = $validated['total_duration'];
        $workout->total_calories_burned = $validated['total_calories_burned'];
        $workout->notes = $validated['notes'];
        $workout->save();
        
        // Delete existing workout items
        $workout->workoutItems()->delete();
        
        // Add new workout items
        foreach ($validated['workout_items'] as $itemData) {
            $workoutItem = new WorkoutItem();
            $workoutItem->workout_id = $workout->id;
            $workoutItem->exercise_type = $itemData['exercise_type'];
            $workoutItem->exercise_id = $itemData['exercise_id'];
            $workoutItem->duration = $itemData['duration'];
            $workoutItem->sets = $itemData['sets'];
            $workoutItem->reps = $itemData['reps'];
            $workoutItem->calories_burned = $itemData['calories_burned'];
            $workoutItem->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Entraînement mis à jour avec succès',
            'workout' => $workout->load('workoutItems')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Workout  $workout
     * @return \Illuminate\Http\Response
     */
    public function destroy(Workout $workout)
    {
        if ($workout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Delete workout items first
        $workout->workoutItems()->delete();
        
        // Delete the workout
        $workout->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Entraînement supprimé avec succès'
        ]);
    }

    /**
     * Clone a workout.
     *
     * @param  \App\Models\Workout  $workout
     * @return \Illuminate\Http\Response
     */
    public function clone(Workout $workout)
    {
        if ($workout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Create a new workout based on the original
        $newWorkout = $workout->replicate();
        $newWorkout->entry_date = Carbon::today()->format('Y-m-d');
        $newWorkout->entry_time = Carbon::now()->format('H:i:s');
        $newWorkout->save();
        
        // Clone workout items
        foreach ($workout->workoutItems as $item) {
            $newItem = $item->replicate();
            $newItem->workout_id = $newWorkout->id;
            $newItem->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Entraînement cloné avec succès',
            'workout' => $newWorkout->load('workoutItems')
        ]);
    }

    /**
     * Add an exercise item to a workout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Workout  $workout
     * @return \Illuminate\Http\Response
     */
    public function addExercise(Request $request, Workout $workout)
    {
        if ($workout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'exercise_type' => 'required|string|in:exercise,custom_exercise',
            'exercise_id' => 'required|integer',
            'duration' => 'required|integer|min:1',
            'sets' => 'nullable|integer|min:1',
            'reps' => 'nullable|integer|min:1',
        ]);
        
        // Calculate calories based on exercise type and duration
        $caloriesBurned = 0;
        if ($validated['exercise_type'] === 'exercise') {
            $exercise = Exercise::findOrFail($validated['exercise_id']);
            $caloriesBurned = $exercise->calories_per_minute * $validated['duration'];
        } else {
            $exercise = CustomExercise::where('user_id', Auth::id())
                ->where('id', $validated['exercise_id'])
                ->firstOrFail();
            $caloriesBurned = $exercise->calories_per_minute * $validated['duration'];
        }
        
        // Create the workout item
        $workoutItem = new WorkoutItem();
        $workoutItem->workout_id = $workout->id;
        $workoutItem->exercise_type = $validated['exercise_type'];
        $workoutItem->exercise_id = $validated['exercise_id'];
        $workoutItem->duration = $validated['duration'];
        $workoutItem->sets = $validated['sets'];
        $workoutItem->reps = $validated['reps'];
        $workoutItem->calories_burned = $caloriesBurned;
        $workoutItem->save();
        
        // Update workout total duration and calories
        $workout->total_duration = $workout->workoutItems->sum('duration');
        $workout->total_calories_burned = $workout->workoutItems->sum('calories_burned');
        $workout->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Exercice ajouté avec succès',
            'workout_item' => $workoutItem,
            'workout' => $workout->load('workoutItems')
        ]);
    }

    /**
     * Remove an exercise item from a workout.
     *
     * @param  \App\Models\Workout  $workout
     * @param  \App\Models\WorkoutItem  $workoutItem
     * @return \Illuminate\Http\Response
     */
    public function removeExercise(Workout $workout, WorkoutItem $workoutItem)
    {
        if ($workout->user_id !== Auth::id() || $workoutItem->workout_id !== $workout->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Delete the workout item
        $workoutItem->delete();
        
        // Update workout total duration and calories
        $workout->total_duration = $workout->workoutItems->sum('duration');
        $workout->total_calories_burned = $workout->workoutItems->sum('calories_burned');
        $workout->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Exercice supprimé avec succès',
            'workout' => $workout->load('workoutItems')
        ]);
    }
}
