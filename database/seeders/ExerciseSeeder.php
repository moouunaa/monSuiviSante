<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exercises = [
            [
                'name' => 'Course à pied (modérée)',
                'calories_per_minute' => 10,
                'category' => 'cardio',
            ],
            [
                'name' => 'Course à pied (intense)',
                'calories_per_minute' => 14,
                'category' => 'cardio',
            ],
            [
                'name' => 'Marche rapide',
                'calories_per_minute' => 5,
                'category' => 'cardio',
            ],
            [
                'name' => 'Vélo (modéré)',
                'calories_per_minute' => 8,
                'category' => 'cardio',
            ],
            [
                'name' => 'Vélo (intense)',
                'calories_per_minute' => 12,
                'category' => 'cardio',
            ],
            [
                'name' => 'Natation',
                'calories_per_minute' => 11,
                'category' => 'cardio',
            ],
            [
                'name' => 'Corde à sauter',
                'calories_per_minute' => 12,
                'category' => 'cardio',
            ],
            [
                'name' => 'Rameur',
                'calories_per_minute' => 10,
                'category' => 'cardio',
            ],
            [
                'name' => 'Elliptique',
                'calories_per_minute' => 8,
                'category' => 'cardio',
            ],
            [
                'name' => 'Squats',
                'calories_per_minute' => 8,
                'category' => 'strength',
            ],
            [
                'name' => 'Pompes',
                'calories_per_minute' => 7,
                'category' => 'strength',
            ],
            [
                'name' => 'Tractions',
                'calories_per_minute' => 8,
                'category' => 'strength',
            ],
            [
                'name' => 'Développé couché',
                'calories_per_minute' => 6,
                'category' => 'strength',
            ],
            [
                'name' => 'Soulevé de terre',
                'calories_per_minute' => 9,
                'category' => 'strength',
            ],
            [
                'name' => 'Burpees',
                'calories_per_minute' => 13,
                'category' => 'strength',
            ],
            [
                'name' => 'Yoga',
                'calories_per_minute' => 4,
                'category' => 'flexibility',
            ],
            [
                'name' => 'Pilates',
                'calories_per_minute' => 5,
                'category' => 'flexibility',
            ],
            [
                'name' => 'Étirements',
                'calories_per_minute' => 2,
                'category' => 'flexibility',
            ],
            [
                'name' => 'Danse',
                'calories_per_minute' => 7,
                'category' => 'cardio',
            ],
            [
                'name' => 'Boxe',
                'calories_per_minute' => 13,
                'category' => 'cardio',
            ],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}