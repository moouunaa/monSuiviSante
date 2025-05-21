<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_name',
        'duration',
        'calories_burned',
        'entry_date',
        'entry_time',
        'notes'
    ];

    protected $casts = [
        'duration' => 'integer',
        'calories_burned' => 'integer',
        'entry_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}