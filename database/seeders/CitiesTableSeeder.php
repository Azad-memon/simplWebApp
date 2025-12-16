<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate table before seeding
        DB::table('cities')->truncate();

        // Insert cities
        DB::table('cities')->insert([
            [
                'city_name' => 'Karachi',
                'citycode' => 'KAR',
                'province' => 'Sindh',
                'population' => 16093786,
                'latitude' => 24.8607,
                'longitude' => 67.0011,
            ],
            [
                'city_name' => 'Lahore',
                'citycode' => 'LAH',
                'province' => 'Punjab',
                'population' => 12188000,
                'latitude' => 31.5497,
                'longitude' => 74.3436,
            ],
            [
                'city_name' => 'Faisalabad',
                'citycode' => 'FAI',
                'province' => 'Punjab',
                'population' => 3203846,
                'latitude' => 31.4180,
                'longitude' => 73.0760,
            ],
            [
                'city_name' => 'Rawalpindi',
                'citycode' => 'RAW',
                'province' => 'Punjab',
                'population' => 2098231,
                'latitude' => 33.6000,
                'longitude' => 73.0479,
            ],
            [
                'city_name' => 'Multan',
                'citycode' => 'MUL',
                'province' => 'Punjab',
                'population' => 1871843,
                'latitude' => 30.1978,
                'longitude' => 71.4697,
            ],
            [
                'city_name' => 'Hyderabad',
                'citycode' => 'HYD',
                'province' => 'Sindh',
                'population' => 1732693,
                'latitude' => 25.3963,
                'longitude' => 68.3639,
            ],
            [
                'city_name' => 'Peshawar',
                'citycode' => 'PES',
                'province' => 'Khyber Pakhtunkhwa',
                'population' => 1970042,
                'latitude' => 34.0144,
                'longitude' => 71.5805,
            ],
            [
                'city_name' => 'Quetta',
                'citycode' => 'QUE',
                'province' => 'Balochistan',
                'population' => 1001200,
                'latitude' => 30.2095,
                'longitude' => 67.0173,
            ],
            [
                'city_name' => 'Sialkot',
                'citycode' => 'SIA',
                'province' => 'Punjab',
                'population' => 920000,
                'latitude' => 32.4945,
                'longitude' => 74.5207,
            ],
            [
                'city_name' => 'Islamabad',
                'citycode' => 'ISA',
                'province' => 'Islamabad Capital Territory',
                'population' => 1110000,
                'latitude' => 33.6844,
                'longitude' => 73.0479,
            ],
        ]);
    }
}
