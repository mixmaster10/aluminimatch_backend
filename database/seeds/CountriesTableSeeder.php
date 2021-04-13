<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            ['id' => 1,     'name' => 'United States'],
            ['id' => 2,     'name' => 'India'],
            ['id' => 3,     'name' => 'Pakistan'],
            ['id' => 4,     'name' => 'Philippines'],
            ['id' => 5,     'name' => 'Nigeria'],
            ['id' => 6,     'name' => 'United Kingdom'],
            ['id' => 7,     'name' => 'Sweden'],
            ['id' => 8,     'name' => 'Netherlands'],
            ['id' => 9,     'name' => 'Denmark'],
            ['id' => 10,    'name' => 'Norway'],
            ['id' => 11,    'name' => 'Finland'],
            ['id' => 12,    'name' => 'Germany'],
            ['id' => 13,    'name' => 'Canada']
        ];

        foreach ($countries as $country) {
            \App\Models\Country::create($country);
        }
    }
}
