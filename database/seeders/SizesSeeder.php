<?php

namespace Database\Seeders;

use App\Models\Size; // Assuming the model is named Size
use Illuminate\Database\Seeder;

class SizesSeeder extends Seeder
{
    public function run()
    {
        // 5 Sample Sizes
        Size::create([
            'name' => 'Small',
            'code' => 'S',
            'is_active' => 1,
        ]);

        Size::create([
            'name' => 'Medium',
            'code' => 'M',
            'is_active' => 1,
        ]);

        Size::create([
            'name' => 'Large',
            'code' => 'L',
            'is_active' => 1,
        ]);

        Size::create([
            'name' => 'Extra Large',
            'code' => 'XL',
            'is_active' => 1,
        ]);

        Size::create([
            'name' => 'Double Extra Large',
            'code' => 'XXL',
            'is_active' => 1,
        ]);
    }
}
