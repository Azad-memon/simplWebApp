<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table(table: 'categories')->truncate();

        $now = Carbon::now();

        // Step 1: Insert the parent category (Coffee)
        $parentId = DB::table('categories')->insertGetId([
            'name' => 'Coffee',
            'desc' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'parent_id' => 0,
            'type' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
            'deleted_at' => null,
        ]);

        // Step 2: Insert subcategories under Coffee
        $subcategories = [
            'For you',
            'New',
            'Hot Latte',
            'Iced Latte',
            'Frappe',
            'Matcha',
            'Iced Tea',
            'Hot Chocolate',
        ];

        foreach ($subcategories as $name) {
            DB::table('categories')->insert([
                'name' => $name,
                'desc' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'parent_id' => $parentId,
                'type' => 0,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }
    }
}
