<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\MealItem;
use App\Models\Food;
use App\Models\CustomFood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $meals = Meal::where('user_id', Auth::id())
            ->with('mealItems.food')
            ->orderBy('entry_date', 'desc')
            ->orderBy('entry_time', 'desc')
            ->paginate(15);
            
        return view('meals.index', compact('meals'));
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
            'meal_items' => 'required|array|min:1',
            'meal_items.*.food_type' => 'required|string',
            'meal_items.*.food_id' => 'required|integer',
            'meal_items.*.quantity' => 'required|numeric|min:0.1',
            'meal_items.*.unit' => 'required|string',
            'meal_items.*.calories' => 'required|integer|min:0',
            'total_calories' => 'required|integer|min:0',
        ]);
        
        // Determine meal type based on time
        $hour = (int)Carbon::parse($validated['entry_time'])->format('H');
        $mealType = 'snack';
        
        if ($hour >= 5 && $hour < 10) {
            $mealType = 'breakfast';
        } elseif ($hour >= 10 && $hour < 15) {
            $mealType = 'lunch';
        } elseif ($hour >= 17 && $hour < 22) {
            $mealType = 'dinner';
        }
        
        // Create the meal
        $meal = new Meal();
        $meal->user_id = Auth::id();
        $meal->meal_type = $mealType;
        $meal->entry_date = $validated['entry_date'];
        $meal->entry_time = $validated['entry_time'];
        $meal->total_calories = $validated['total_calories'];
        $meal->notes = $validated['notes'];
        $meal->save();
        
        // Add meal items
        foreach ($validated['meal_items'] as $itemData) {
            $mealItem = new MealItem();
            $mealItem->meal_id = $meal->id;
            $mealItem->food_type = $itemData['food_type'];
            $mealItem->food_id = $itemData['food_id'];
            $mealItem->quantity = $itemData['quantity'];
            $mealItem->unit = $itemData['unit'];
            $mealItem->calories = $itemData['calories'];
            $mealItem->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Repas enregistré avec succès',
            'meal' => $meal->load('mealItems')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function show(Meal $meal)
    {
        if ($meal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $meal->load('mealItems.food');
        
        return view('meals.show', compact('meal'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Meal $meal)
    {
        if ($meal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'entry_time' => 'required',
            'notes' => 'nullable|string',
            'meal_items' => 'required|array|min:1',
            'meal_items.*.food_type' => 'required|string',
            'meal_items.*.food_id' => 'required|integer',
            'meal_items.*.quantity' => 'required|numeric|min:0.1',
            'meal_items.*.unit' => 'required|string',
            'meal_items.*.calories' => 'required|integer|min:0',
            'total_calories' => 'required|integer|min:0',
        ]);
        
        // Determine meal type based on time
        $hour = (int)Carbon::parse($validated['entry_time'])->format('H');
        $mealType = 'snack';
        
        if ($hour >= 5 && $hour < 10) {
            $mealType = 'breakfast';
        } elseif ($hour >= 10 && $hour < 15) {
            $mealType = 'lunch';
        } elseif ($hour >= 17 && $hour < 22) {
            $mealType = 'dinner';
        }
        
        // Update the meal
        $meal->meal_type = $mealType;
        $meal->entry_date = $validated['entry_date'];
        $meal->entry_time = $validated['entry_time'];
        $meal->total_calories = $validated['total_calories'];
        $meal->notes = $validated['notes'];
        $meal->save();
        
        // Delete existing meal items
        $meal->mealItems()->delete();
        
        // Add new meal items
        foreach ($validated['meal_items'] as $itemData) {
            $mealItem = new MealItem();
            $mealItem->meal_id = $meal->id;
            $mealItem->food_type = $itemData['food_type'];
            $mealItem->food_id = $itemData['food_id'];
            $mealItem->quantity = $itemData['quantity'];
            $mealItem->unit = $itemData['unit'];
            $mealItem->calories = $itemData['calories'];
            $mealItem->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Repas mis à jour avec succès',
            'meal' => $meal->load('mealItems')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meal $meal)
    {
        if ($meal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Delete meal items first
        $meal->mealItems()->delete();
        
        // Delete the meal
        $meal->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Repas supprimé avec succès'
        ]);
    }

    /**
     * Clone a meal.
     *
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function clone(Meal $meal)
    {
        if ($meal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Create a new meal based on the original
        $newMeal = $meal->replicate();
        $newMeal->entry_date = Carbon::today()->format('Y-m-d');
        $newMeal->entry_time = Carbon::now()->format('H:i:s');
        $newMeal->save();
        
        // Clone meal items
        foreach ($meal->mealItems as $item) {
            $newItem = $item->replicate();
            $newItem->meal_id = $newMeal->id;
            $newItem->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Repas cloné avec succès',
            'meal' => $newMeal->load('mealItems')
        ]);
    }

    /**
     * Add a food item to a meal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function addFood(Request $request, Meal $meal)
    {
        if ($meal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'food_type' => 'required|string|in:food,custom_food',
            'food_id' => 'required|integer',
            'quantity' => 'required|numeric|min:0.1',
            'unit' => 'required|string',
        ]);
        
        // Calculate calories based on food type and quantity
        $calories = 0;
        if ($validated['food_type'] === 'food') {
            $food = Food::findOrFail($validated['food_id']);
            $calories = round($food->calories_per_100g * $validated['quantity'] / 100);
        } else {
            $food = CustomFood::where('user_id', Auth::id())
                ->where('id', $validated['food_id'])
                ->firstOrFail();
            $calories = round($food->calories_per_100g * $validated['quantity'] / 100);
        }
        
        // Create the meal item
        $mealItem = new MealItem();
        $mealItem->meal_id = $meal->id;
        $mealItem->food_type = $validated['food_type'];
        $mealItem->food_id = $validated['food_id'];
        $mealItem->quantity = $validated['quantity'];
        $mealItem->unit = $validated['unit'];
        $mealItem->calories = $calories;
        $mealItem->save();
        
        // Update meal total calories
        $meal->total_calories = $meal->mealItems->sum('calories');
        $meal->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Aliment ajouté avec succès',
            'meal_item' => $mealItem,
            'meal' => $meal->load('mealItems')
        ]);
    }

    /**
     * Remove a food item from a meal.
     *
     * @param  \App\Models\Meal  $meal
     * @param  \App\Models\MealItem  $mealItem
     * @return \Illuminate\Http\Response
     */
    public function removeFood(Meal $meal, MealItem $mealItem)
    {
        if ($meal->user_id !== Auth::id() || $mealItem->meal_id !== $meal->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Delete the meal item
        $mealItem->delete();
        
        // Update meal total calories
        $meal->total_calories = $meal->mealItems->sum('calories');
        $meal->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Aliment supprimé avec succès',
            'meal' => $meal->load('mealItems')
        ]);
    }
}
