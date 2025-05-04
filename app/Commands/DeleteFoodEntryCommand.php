<?php

namespace App\Commands;

use App\Models\FoodEntry;

class DeleteFoodEntryCommand implements CommandInterface
{
    private $entryId;
    private $userId;
    
    public function __construct($entryId, $userId)
    {
        $this->entryId = $entryId;
        $this->userId = $userId;
    }
    
    public function execute()
    {
        $entry = FoodEntry::where('id', $this->entryId)
            ->where('user_id', $this->userId)
            ->firstOrFail();
            
        $entry->delete();
        
        return true;
    }
}