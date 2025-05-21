<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SleepEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sleep_date',
        'sleep_time',
        'wake_time',
        'duration',
        'quality',
        'notes'
    ];

    protected $casts = [
        'sleep_date' => 'date',
        'duration' => 'integer',
        'quality' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}