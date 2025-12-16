<?php
// database/seeders/IngredientsSeeder.php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientsSeeder extends Seeder
{
     public function run(): void
    {
        DB::table(table: 'ingredients')->truncate();
        $ingredients = [
            'Coffee',
            'Milk (Dayfresh)',
            'Milk (Olpers)',
            'Cup',
            'Condensed Milk',
            'Any Syrup',
            'Vanilla Frappe',
            'Chocolate Frappe',
            'Ice',
            'Peanut Butter',
            'White Chocolate Sauce',
            'Teabag (Lipton)',
            'Water',
            'Matcha (Ceremonial)',
            'Whipped Cream',
        ];

        foreach ($ingredients as $name) {
            Ingredient::create([
                'ing_name'    => $name,
                'ing_desc'    => 'Commonly used ingredient.',
                'ing_unit'    => rand(1, 10), // Random unit ID or measurement
                'is_active'   => 1,
                'created_by'  => 1,
            ]);
        }
    }
    // public function run()
    // {
    //     DB::table('ingredients')->truncate();

    //     // 50 Sample Ingredients
    //     Ingredient::create([
    //         'ing_name' => 'Espresso',
    //         'ing_desc' => 'A concentrated coffee brewed by forcing a small amount of nearly boiling water through finely ground coffee beans.',
    //         'ing_unit' => rand(1, 10),
    //         'is_active' => 1,
    //         'created_by' => 1, // Assuming the user ID for "created_by"
    //     ]);

    //     Ingredient::create([
    //         'ing_name' => 'Milk',
    //         'ing_desc' => 'Dairy product obtained from cows, used in coffee-based drinks.',
    //         'ing_unit' => rand(1, 10),
    //         'is_active' => 1,
    //         'created_by' => 1,
    //     ]);

    //     Ingredient::create([
    //         'ing_name' => 'Sugar',
    //         'ing_desc' => 'A sweetener commonly used in coffee, made from sugar cane or beets.',
    //         'ing_unit' => rand(1, 10),
    //         'is_active' => 1,
    //         'created_by' => 1,
    //     ]);

    //     Ingredient::create([
    //         'ing_name' => 'Caramel Syrup',
    //         'ing_desc' => 'A sweet and buttery syrup used for flavoring coffee drinks.',
    //         'ing_unit' => rand(1, 10),
    //         'is_active' => 1,
    //         'created_by' => 1,
    //     ]);

    //     Ingredient::create([
    //         'ing_name' => 'Vanilla Syrup',
    //         'ing_desc' => 'Sweet syrup made with vanilla flavor, often used in coffee drinks.',
    //         'ing_unit' => rand(1, 10),
    //         'is_active' => 1,
    //         'created_by' => 1,
    //     ]);

    //     Ingredient::create([
    //         'ing_name' => 'Cinnamon',
    //         'ing_desc' => 'A spice obtained from the inner bark of several tree species, often used in coffee drinks.',
    //         'ing_unit' => rand(1, 10),
    //         'is_active' => 1,
    //         'created_by' => 1,
    //     ]);

    //     Ingredient::create([
    //         'ing_name' => 'Chocolate Syrup',
    //         'ing_desc' => 'Sweet syrup made with chocolate, used to flavor coffee drinks.',
    //         'ing_unit' => rand(1, 10),
    //         'is_active' => 1,
    //         'created_by' => 1,
    //     ]);

    //     Ingredient::create([
    //         'ing_name' => 'Almond Milk',
    //         'ing_desc' => 'A dairy-free alternative to regular milk, made from almonds.',
    //         'ing_unit' => rand(1, 10),
    //         'is_active' => 1,
    //         'created_by' => 1,
    //     ]);

    //     Ingredient::create([
    //         'ing_name' => 'Oat Milk',
    //         'ing_desc' => 'A dairy-free milk alternative made from oats.',
    //         'ing_unit' => rand(1, 10),
    //         'is_active' => 1,
    //         'created_by' => 1,
    //     ]);

    //     Ingredient::create([
    //         'ing_name' => 'Whipped Cream',
    //         'ing_desc' => 'Cream that has been beaten into a fluffy texture, often used as a topping for coffee drinks.',
    //         'ing_unit' => rand(1, 10),
    //         'is_active' => 1,
    //         'created_by' => 1,
    //     ]);

    //     // Repeat this pattern for 50 ingredients
    //     for ($i = 1; $i <= 40; $i++) {
    //         Ingredient::create([
    //             'ing_name' => 'Ingredient ' . $i,
    //             'ing_desc' => 'Description for Ingredient ' . $i,
    //             'ing_unit' => rand(1, 10),
    //             'is_active' => rand(0, 1), // Random active status
    //             'created_by' => 1, // Assuming user 1 is creating the ingredients
    //         ]);
    //     }
    // }
}
