<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
          // Remove all existing roles before inserting new ones
        DB::table('roles')->truncate();

        DB::table('roles')->insert([
            ['name' => 'Admin'],
            ['name' => 'branchadmin'],
            ['name' => 'customer'],
            ['name' =>"waiter"],
            ['name' =>"accountant" ],
            ['name' =>"dispatcher" ],
        ]);
    }
}
