<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {

        DB::table(table: 'products')->truncate();

        $category = Category::where('name', 'Hot Latte')->first();

        if (!$category) {
            $this->command->error(string: 'Category "Hot Latte" not found.');
            return;
        }

        $products = [
            'Americano',
            'Latte',
            'Spanish',
            'French Vanilla',
            'Caramel Macchiato',
            'Irish Hazelnut',
            'Coconut Crème',
            'Danish Gingerbread',
            'Cinnamon Roll',
            'Roman Tiramisu',
            'Bounty',
            'White Mocha',
            'Ferrero',
        ];

        foreach ($products as $productName) {
            Product::create([
                'name' => $productName,
                'cat_id' => $category->id,
                'product_type' => 'indoor',
                'slug' => Str::slug($productName . "_" . $category->name),
                'desc' => 'A signature beverage with rich flavor and aroma.',
                'is_active' => 1,
            ]);
        }

        $category = Category::where('name', 'Iced Latte')->first();

        if (!$category) {
            $this->command->error('Category "Iced Latte" not found.');
            return;
        }

        $products = [
            'Americano',
            'Latte',
            'Spanish',
            'French Vanilla',
            'Caramel Macchiato',
            'Irish Hazelnut',
            'Coconut Crème',
            'Danish Gingerbread',
            'Cinnamon Roll',
            'Roman Tiramisu',
            'Bounty',
            'White Mocha',
            'Ferrero',
        ];

        foreach ($products as $productName) {
            Product::create([
                'name' => $productName,
                'cat_id' => $category->id,
                'product_type' => 'indoor',
                'slug' => Str::slug($productName . "_" . $category->name),
                'desc' => 'A signature beverage with rich flavor and aroma.',
                'is_active' => 1,
            ]);
        }


        $category = Category::where('name', 'Frappe')->first();

        if (!$category) {
            $this->command->error('Category "Frappe" not found.');
            return;
        }

        $products = [
            'French Vanilla',
            'Caramel Macchiato',
            'Irish Hazelnut',
            'Coconut Crème',
            'Danish Gingerbread',
            'Cinnamon Roll',
            'Bounty',
            'White Mocha',
            'Ferrero',
            'Snickers'
        ];

        foreach ($products as $productName) {
            Product::create([
                'name' => $productName,
                'cat_id' => $category->id,
                'product_type' => 'indoor',
                'slug' => Str::slug($productName . "_" . $category->name),
                'desc' => 'A signature beverage with rich flavor and aroma.',
                'is_active' => 1,
            ]);
        }

        $category = Category::where('name', 'Matcha')->first();

        if (!$category) {
            $this->command->error('Category "Matcha" not found.');
            return;
        }

        $products = [
            'Hot Spanish',
            'Iced Vanilla',
            'Iced Strawberry Rose',
            'Iced Blueberry Lavender'
        ];

        foreach ($products as $productName) {
            Product::create([
                'name' => $productName,
                'cat_id' => $category->id,
                'product_type' => 'indoor',
                'slug' => Str::slug($productName . "_" . $category->name),
                'desc' => 'A signature beverage with rich flavor and aroma.',
                'is_active' => 1,
            ]);
        }


        $category = Category::where('name', 'Iced Tea')->first();

        if (!$category) {
            $this->command->error('Category "Iced Tea" not found.');
            return;
        }

        $products = [
            'Peachy Passion',
            'Double Berry'
        ];

        foreach ($products as $productName) {
            Product::create([
                'name' => $productName,
                'cat_id' => $category->id,
                'product_type' => 'indoor',
                'slug' => Str::slug($productName . "_" . $category->name),
                'desc' => 'A signature beverage with rich flavor and aroma.',
                'is_active' => 1,
            ]);
        }

        $category = Category::where('name', 'Hot Chocolate')->first();

        if (!$category) {
            $this->command->error('Category "Hot Chocolate" not found.');
            return;
        }

        $products = [
            'Belgian',
            'Sea Salt White'
        ];

        foreach ($products as $productName) {
            Product::create([
                'name' => $productName,
                'cat_id' => $category->id,
                'product_type' => 'indoor',
                'slug' => Str::slug($productName . "_" . $category->name),
                'desc' => 'A signature beverage with rich flavor and aroma.',
                'is_active' => 1,
            ]);
        }
    }
}
