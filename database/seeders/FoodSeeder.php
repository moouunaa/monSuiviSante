<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Food;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $foods = [
            [
                'name' => 'Poulet grillé',
                'calories_per_100g' => 165,
                'protein_per_100g' => 31,
                'carbs_per_100g' => 0,
                'fat_per_100g' => 3.6,
                'serving_size' => '100g',
                'calories_per_serving' => 165,
            ],
            [
                'name' => 'Riz blanc cuit',
                'calories_per_100g' => 130,
                'protein_per_100g' => 2.7,
                'carbs_per_100g' => 28,
                'fat_per_100g' => 0.3,
                'serving_size' => '100g',
                'calories_per_serving' => 130,
            ],
            [
                'name' => 'Brocoli cuit',
                'calories_per_100g' => 35,
                'protein_per_100g' => 2.4,
                'carbs_per_100g' => 7.2,
                'fat_per_100g' => 0.4,
                'serving_size' => '100g',
                'calories_per_serving' => 35,
            ],
            [
                'name' => 'Saumon',
                'calories_per_100g' => 208,
                'protein_per_100g' => 20,
                'carbs_per_100g' => 0,
                'fat_per_100g' => 13,
                'serving_size' => '100g',
                'calories_per_serving' => 208,
            ],
            [
                'name' => 'Œuf',
                'calories_per_100g' => 155,
                'protein_per_100g' => 13,
                'carbs_per_100g' => 1.1,
                'fat_per_100g' => 11,
                'serving_size' => '1 œuf (50g)',
                'calories_per_serving' => 78,
            ],
            [
                'name' => 'Pain complet',
                'calories_per_100g' => 247,
                'protein_per_100g' => 13,
                'carbs_per_100g' => 41,
                'fat_per_100g' => 3.3,
                'serving_size' => '1 tranche (40g)',
                'calories_per_serving' => 99,
            ],
            [
                'name' => 'Pomme',
                'calories_per_100g' => 52,
                'protein_per_100g' => 0.3,
                'carbs_per_100g' => 14,
                'fat_per_100g' => 0.2,
                'serving_size' => '1 pomme moyenne (150g)',
                'calories_per_serving' => 78,
            ],
            [
                'name' => 'Banane',
                'calories_per_100g' => 89,
                'protein_per_100g' => 1.1,
                'carbs_per_100g' => 23,
                'fat_per_100g' => 0.3,
                'serving_size' => '1 banane moyenne (120g)',
                'calories_per_serving' => 107,
            ],
            [
                'name' => 'Yaourt nature',
                'calories_per_100g' => 59,
                'protein_per_100g' => 3.5,
                'carbs_per_100g' => 4.7,
                'fat_per_100g' => 3.3,
                'serving_size' => '1 pot (125g)',
                'calories_per_serving' => 74,
            ],
            [
                'name' => 'Fromage cheddar',
                'calories_per_100g' => 402,
                'protein_per_100g' => 25,
                'carbs_per_100g' => 1.3,
                'fat_per_100g' => 33,
                'serving_size' => '30g',
                'calories_per_serving' => 121,
            ],
            [
                'name' => 'Lait demi-écrémé',
                'calories_per_100g' => 46,
                'protein_per_100g' => 3.3,
                'carbs_per_100g' => 4.8,
                'fat_per_100g' => 1.5,
                'serving_size' => '1 verre (250ml)',
                'calories_per_serving' => 115,
            ],
            [
                'name' => 'Pâtes cuites',
                'calories_per_100g' => 158,
                'protein_per_100g' => 5.8,
                'carbs_per_100g' => 31,
                'fat_per_100g' => 0.9,
                'serving_size' => '100g',
                'calories_per_serving' => 158,
            ],
            [
                'name' => 'Avocat',
                'calories_per_100g' => 160,
                'protein_per_100g' => 2,
                'carbs_per_100g' => 8.5,
                'fat_per_100g' => 14.7,
                'serving_size' => '1/2 avocat (70g)',
                'calories_per_serving' => 112,
            ],
            [
                'name' => 'Amandes',
                'calories_per_100g' => 579,
                'protein_per_100g' => 21,
                'carbs_per_100g' => 22,
                'fat_per_100g' => 49,
                'serving_size' => '30g',
                'calories_per_serving' => 174,
            ],
            [
                'name' => 'Chocolat noir (70% cacao)',
                'calories_per_100g' => 598,
                'protein_per_100g' => 7.8,
                'carbs_per_100g' => 46,
                'fat_per_100g' => 43,
                'serving_size' => '1 carré (10g)',
                'calories_per_serving' => 60,
            ],
        ];

        foreach ($foods as $food) {
            Food::create($food);
        }
    }
}