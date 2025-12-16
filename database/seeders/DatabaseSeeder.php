<?php

namespace Database\Seeders;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Database\Seeders\ProductVariantsSeeder;  // Import the ProductVariantsSeeder
use Database\Seeders\UnitsSeeder;            // Import the UnitsSeeder
use Database\Seeders\SizesSeeder;            // Import the SizesSeeder
use Database\Seeders\IngredientsSeeder;      // Import the IngredientsSeeder
use Database\Seeders\ProductSeeder;         // Import the ProductsSeeder
use Database\Seeders\CitiesTableSeeder;  // Import the CitiesTableSeeder




class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       if (!User::where('email', 'admin@gmail.com')->exists()) {
            User::create([
                'first_name' => 'admin',
                'last_name'  => 'admin',
                'email'      => 'admin@gmail.com',
                'password'   => Hash::make('123'),
                'role_id'    => 1,
            ]);
        }
            DB::table('roles')->truncate();
            DB::table('roles')->insert([
                ['name' => 'admin'],
                ['name' => 'branchadmin'],
                ['name' => 'customer'],
                ['name'=> 'waiter'],
                ['name' => 'accountant'],
            ]);
            $this->call(CitiesTableSeeder::class);

        //      $this->call([
        //     ProductSeeder::class,        // ProductSeeder
        //     UnitsSeeder::class,           // UnitsSeeder
        //     SizesSeeder::class,           // SizesSeeder
        //     IngredientsSeeder::class,     // IngredientsSeeder
        //     ProductVariantsSeeder::class, // ProductVariantsSeeder
        // ]);

    }
}
