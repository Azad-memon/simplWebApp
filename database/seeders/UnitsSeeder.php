<?php

// database/seeders/UnitsSeeder.php

namespace Database\Seeders;

use App\Models\Unit; // Assuming you have a Unit model
use Illuminate\Database\Seeder;

class UnitsSeeder extends Seeder
{
    public function run()
    {
        // 50 Sample Units
        Unit::create([
            'name' => 'Liter',
            'symbol' => 'L',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Gram',
            'symbol' => 'g',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Cup',
            'symbol' => 'cup',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Milliliter',
            'symbol' => 'ml',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Kilogram',
            'symbol' => 'kg',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Ounce',
            'symbol' => 'oz',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Pound',
            'symbol' => 'lb',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Teaspoon',
            'symbol' => 'tsp',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Tablespoon',
            'symbol' => 'tbsp',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Inch',
            'symbol' => 'in',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Centimeter',
            'symbol' => 'cm',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Millimeter',
            'symbol' => 'mm',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Meter',
            'symbol' => 'm',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Kilometer',
            'symbol' => 'km',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Teaspoon (UK)',
            'symbol' => 'tsp (UK)',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Cup (US)',
            'symbol' => 'cup (US)',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Pint',
            'symbol' => 'pt',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Quart',
            'symbol' => 'qt',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Gallon',
            'symbol' => 'gal',
            'is_active' => 1,
        ]);

        Unit::create([
            'name' => 'Milligram',
            'symbol' => 'mg',
            'is_active' => 1,
        ]);

        // Repeat this pattern for more units (total 50)
        for ($i = 1; $i <= 30; $i++) {
            Unit::create([
                'name' => 'Unit ' . $i,
                'symbol' => 'sym' . $i,
                'is_active' => rand(0, 1), // Random active/inactive status
            ]);
        }
    }
}
