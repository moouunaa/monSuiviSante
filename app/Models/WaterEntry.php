<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'entry_date',
        'entry_time',
        'notes'
    ];

    protected $casts = [
        'amount' => 'integer',
        'entry_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}