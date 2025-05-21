<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weight',
        'entry_date',
        'notes'
    ];

    protected $casts = [
        'weight' => 'float',
        'entry_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}