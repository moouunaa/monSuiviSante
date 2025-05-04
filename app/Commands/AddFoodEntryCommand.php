<?php

namespace App\Commands;

use App\Models\FoodEntry;
use Carbon\Carbon;

class AddFoodEntryCommand implements CommandInterface
{
    private $userId;
    private $data;
    
    public function __construct($userId, array $data)
    {
        $this->userId = $userId;
        $this->data = $data;
    }
    
    public function execute()
    {
        $foodEntry = new FoodEntry($this->data);
        $foodEntry->user_id = $this->userId;
        $foodEntry->entry_date = Carbon::today()->format('Y-m-d');
        $foodEntry->entry_time = Carbon::now()->format('H:i:s');
        $foodEntry->save();
        
        return $foodEntry;
    }
}