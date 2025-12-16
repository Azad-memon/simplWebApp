<?php
// database/seeders/ProductVariantsSeeder.php
// database/seeders/ProductVariantsSeeder.php

namespace Database\Seeders;

use App\Models\ProductVariant;  // Assuming you have the ProductVariant model
use App\Models\Unit;            // Assuming the Unit model exists
use App\Models\Size;            // Assuming the Size model exists
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantsSeeder extends Seeder
{
    public function run()
    {

        DB::table(table: 'ingredients')->truncate();

        // Fetch units and sizes from the database
        $units = Unit::all();
        $sizes = Size::all();

        // Ensure there are at least 5 units and sizes available
        if ($units->count() < 5 || $sizes->count() < 5) {
            echo "Insufficient units or sizes in the database.";
            return;
        }

        // Ensure the random combination of unit and size does not repeat
        $usedCombinations = [];

        for ($i = 1; $i <= 50; $i++) {
            // Generate a unique random unit-size combination
            do {
                $unit = $units->random(); // Randomly select a unit
                $size = $sizes->random(); // Randomly select a size
                $combination = $unit->id . '-' . $size->id; // Concatenate the unit and size IDs
            } while (in_array($combination, $usedCombinations)); // Ensure the combination is not repeated

            // Store the combination to prevent repetition
            $usedCombinations[] = $combination;

            // Create a product variant with unique unit-size combinations
            ProductVariant::create([
                'product_id' => rand(1, 10), // Assuming there are at least 10 products, adjust the range as needed
                'unit' => $unit->id,         // Unit ID from the units table
                'size' => $size->id,         // Size ID from the sizes table
                'sku' => 'SKU' . str_pad($i, 3, '0', STR_PAD_LEFT), // Example SKU (SKU001, SKU002, etc.)
                'price' => rand(5, 100),     // Random price between 5 and 100
                'is_active' => rand(0, 1),   // Random active status (0 or 1)
            ]);
        }
    }
}
