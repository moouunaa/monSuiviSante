<?php

namespace App\Http\Controllers;

use App\Models\CustomFood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomFoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customFoods = CustomFood::where('user_id', Auth::id())->get();
        return view('custom-foods.index', compact('customFoods'));
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
            'calories_per_100g' => 'required|integer|min:1',
            'protein_per_100g' => 'nullable|numeric|min:0',
            'carbs_per_100g' => 'nullable|numeric|min:0',
            'fat_per_100g' => 'nullable|numeric|min:0',
            'serving_size' => 'nullable|string|max:255',
            'calories_per_serving' => 'nullable|integer|min:1',
        ]);
        
        $customFood = new CustomFood();
        $customFood->user_id = Auth::id();
        $customFood->name = $validated['name'];
        $customFood->calories_per_100g = $validated['calories_per_100g'];
        $customFood->protein_per_100g = $validated['protein_per_100g'] ?? null;
        $customFood->carbs_per_100g = $validated['carbs_per_100g'] ?? null;
        $customFood->fat_per_100g = $validated['fat_per_100g'] ?? null;
        $customFood->serving_size = $validated['serving_size'] ?? null;
        $customFood->calories_per_serving = $validated['calories_per_serving'] ?? null;
        $customFood->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Aliment personnalisé créé avec succès',
            'customFood' => $customFood
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
        $customFood = CustomFood::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
            
        return view('custom-foods.show', compact('customFood'));
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
        $customFood = CustomFood::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
            
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'calories_per_100g' => 'required|integer|min:1',
            'protein_per_100g' => 'nullable|numeric|min:0',
            'carbs_per_100g' => 'nullable|numeric|min:0',
            'fat_per_100g' => 'nullable|numeric|min:0',
            'serving_size' => 'nullable|string|max:255',
            'calories_per_serving' => 'nullable|integer|min:1',
        ]);
        
        $customFood->name = $validated['name'];
        $customFood->calories_per_100g = $validated['calories_per_100g'];
        $customFood->protein_per_100g = $validated['protein_per_100g'] ?? null;
        $customFood->carbs_per_100g = $validated['carbs_per_100g'] ?? null;
        $customFood->fat_per_100g = $validated['fat_per_100g'] ?? null;
        $customFood->serving_size = $validated['serving_size'] ?? null;
        $customFood->calories_per_serving = $validated['calories_per_serving'] ?? null;
        $customFood->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Aliment personnalisé mis à jour avec succès',
            'customFood' => $customFood
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
        $customFood = CustomFood::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
            
        $customFood->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Aliment personnalisé supprimé avec succès'
        ]);
    }
}
