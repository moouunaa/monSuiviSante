<?php

namespace App\Prototype;

use App\Models\FoodEntry;
use Carbon\Carbon;

class FoodEntryPrototype
{
    private $foodEntry;
    
    public function __construct(FoodEntry $foodEntry)
    {
        $this->foodEntry = $foodEntry;
    }
    
    public function clone(): FoodEntry
    {
        $newEntry = new FoodEntry();
        
        // Copier les attributs
        $newEntry->user_id = $this->foodEntry->user_id;
        $newEntry->food_name = $this->foodEntry->food_name;
        $newEntry->meal_type = $this->foodEntry->meal_type;
        $newEntry->calories = $this->foodEntry->calories;
        $newEntry->portion_size = $this->foodEntry->portion_size;
        
        // Mettre à jour la date et l'heure
        $newEntry->entry_date = Carbon::today()->format('Y-m-d');
        $newEntry->entry_time = Carbon::now()->format('H:i:s');
        
        return $newEntry;
    }
}