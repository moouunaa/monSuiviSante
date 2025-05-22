<?php

namespace App\Http\Controllers;

use App\Models\CustomExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customExercises = CustomExercise::where('user_id', Auth::id())->get();
        return view('custom-exercises.index', compact('customExercises'));
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
            'name' => 'required|string|max:255',
            'calories_per_minute' => 'required|integer|min:1',
            'category' => 'required|string|max:255',
        ]);
        
        $customExercise = new CustomExercise();
        $customExercise->user_id = Auth::id();
        $customExercise->name = $validated['name'];
        $customExercise->calories_per_minute = $validated['calories_per_minute'];
        $customExercise->category = $validated['category'];
        $customExercise->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Exercice personnalisé créé avec succès',
            'customExercise' => $customExercise
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customExercise = CustomExercise::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
            
        return view('custom-exercises.show', compact('customExercise'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customExercise = CustomExercise::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
            
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'calories_per_minute' => 'required|integer|min:1',
            'category' => 'required|string|max:255',
        ]);
        
        $customExercise->name = $validated['name'];
        $customExercise->calories_per_minute = $validated['calories_per_minute'];
        $customExercise->category = $validated['category'];
        $customExercise->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Exercice personnalisé mis à jour avec succès',
            'customExercise' => $customExercise
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customExercise = CustomExercise::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
            
        $customExercise->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Exercice personnalisé supprimé avec succès'
        ]);
    }
}
