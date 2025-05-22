<?php

namespace App\Commands;

// Interface for all workout commands
interface WorkoutCommand
{
    public function execute();
    public function undo();
}

// Command to add an exercise to a workout
class AddExerciseToWorkoutCommand implements WorkoutCommand
{
    protected $workout;
    protected $exercise;
    protected $duration;
    protected $sets;
    protected $reps;
    protected $workoutItem;

    public function __construct($workout, $exercise, $duration, $sets = null, $reps = null)
    {
        $this->workout = $workout;
        $this->exercise = $exercise;
        $this->duration = $duration;
        $this->sets = $sets;
        $this->reps = $reps;
    }

    public function execute()
    {
        $caloriesBurned = $this->exercise->calculateCaloriesBurned($this->duration);
        
        $this->workoutItem = new \App\Models\WorkoutItem([
            'workout_id' => $this->workout->id,
            'duration' => $this->duration,
            'sets' => $this->sets,
            'reps' => $this->reps,
            'calories_burned' => $caloriesBurned
        ]);
        
        $this->workoutItem->exercise()->associate($this->exercise);
        $this->workoutItem->save();
        
        $this->workout->updateTotals();
        
        return $this->workoutItem;
    }

    public function undo()
    {
        if ($this->workoutItem) {
            $this->workoutItem->delete();
            $this->workout->updateTotals();
        }
    }
}

// Command to remove an exercise from a workout
class RemoveExerciseFromWorkoutCommand implements WorkoutCommand
{
    protected $workoutItem;
    protected $workout;
    protected $savedWorkoutItem;

    public function __construct($workoutItem)
    {
        $this->workoutItem = $workoutItem;
        $this->workout = $workoutItem->workout;
    }

    public function execute()
    {
        // Save a copy for potential undo
        $this->savedWorkoutItem = $this->workoutItem->replicate();
        
        $this->workoutItem->delete();
        $this->workout->updateTotals();
    }

    public function undo()
    {
        if ($this->savedWorkoutItem) {
            $newItem = new \App\Models\WorkoutItem([
                'workout_id' => $this->workout->id,
                'duration' => $this->savedWorkoutItem->duration,
                'sets' => $this->savedWorkoutItem->sets,
                'reps' => $this->savedWorkoutItem->reps,
                'calories_burned' => $this->savedWorkoutItem->calories_burned
            ]);
            
            $newItem->exercise()->associate($this->savedWorkoutItem->exercise);
            $newItem->save();
            
            $this->workout->updateTotals();
        }
    }
}

// Command invoker to manage workout commands
class WorkoutCommandInvoker
{
    protected $commands = [];
    protected $history = [];

    public function executeCommand(WorkoutCommand $command)
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