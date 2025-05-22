<?php

namespace App\Commands;

// Interface for all meal commands
interface MealCommand
{
    public function execute();
    public function undo();
}

// Command to add a food item to a meal
class AddFoodToMealCommand implements MealCommand
{
    protected $meal;
    protected $food;
    protected $quantity;
    protected $unit;
    protected $mealItem;

    public function __construct($meal, $food, $quantity, $unit)
    {
        $this->meal = $meal;
        $this->food = $food;
        $this->quantity = $quantity;
        $this->unit = $unit;
    }

    public function execute()
    {
        $calories = $this->food->calculateCalories($this->quantity, $this->unit);
        
        $this->mealItem = new \App\Models\MealItem([
            'meal_id' => $this->meal->id,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'calories' => $calories
        ]);
        
        $this->mealItem->food()->associate($this->food);
        $this->mealItem->save();
        
        $this->meal->updateTotalCalories();
        
        return $this->mealItem;
    }

    public function undo()
    {
        if ($this->mealItem) {
            $this->mealItem->delete();
            $this->meal->updateTotalCalories();
        }
    }
}

// Command to remove a food item from a meal
class RemoveFoodFromMealCommand implements MealCommand
{
    protected $mealItem;
    protected $meal;
    protected $savedMealItem;

    public function __construct($mealItem)
    {
        $this->mealItem = $mealItem;
        $this->meal = $mealItem->meal;
    }

    public function execute()
    {
        // Save a copy for potential undo
        $this->savedMealItem = $this->mealItem->replicate();
        
        $this->mealItem->delete();
        $this->meal->updateTotalCalories();
    }

    public function undo()
    {
        if ($this->savedMealItem) {
            $newItem = new \App\Models\MealItem([
                'meal_id' => $this->meal->id,
                'quantity' => $this->savedMealItem->quantity,
                'unit' => $this->savedMealItem->unit,
                'calories' => $this->savedMealItem->calories
            ]);
            
            $newItem->food()->associate($this->savedMealItem->food);
            $newItem->save();
            
            $this->meal->updateTotalCalories();
        }
    }
}

// Command invoker to manage meal commands
class MealCommandInvoker
{
    protected $commands = [];
    protected $history = [];

    public function executeCommand(MealCommand $command)
    {
        $result = $command->execute();
        $this->history[] = $command;
        return $result;
    }

    public function undoLastCommand()
    {
        if (!empty($this->history)) {
            $command = array_pop($this->history);
            $command->undo();
        }
    }
}