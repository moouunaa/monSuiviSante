<?php

namespace App\Commands;

use App\Models\FoodEntry;
use App\Prototype\FoodEntryPrototype;

class CloneFoodEntryCommand implements CommandInterface
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
        $originalEntry = FoodEntry::where('id', $this->entryId)
            ->where('user_id', $this->userId)
            ->firstOrFail();
            
        $prototype = new FoodEntryPrototype($originalEntry);
        $newEntry = $prototype->clone();
        $newEntry->save();
        
        return $newEntry;
    }
}