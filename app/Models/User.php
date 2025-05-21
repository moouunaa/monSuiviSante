<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function foodEntries()
    {
        return $this->hasMany(FoodEntry::class);
    }

    public function weightEntries()
    {
        return $this->hasMany(WeightEntry::class);
    }

    public function exerciseEntries()
    {
        return $this->hasMany(ExerciseEntry::class);
    }

    public function waterEntries()
    {
        return $this->hasMany(WaterEntry::class);
    }

    public function sleepEntries()
    {
        return $this->hasMany(SleepEntry::class);
    }
}