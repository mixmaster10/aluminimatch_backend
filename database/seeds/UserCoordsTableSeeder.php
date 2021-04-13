<?php

use Illuminate\Database\Seeder;

class UserCoordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_coords = [
            [
                'uid' => 1,
                'lat' => 38.9965049,
                'lng' => -77.0189145,
                'show' => 1,
                'radius' => 20
            ]
        ];

        foreach ($user_coords as $coords) {
            \App\Models\UserCoords::create($coords);
        }
    }
}
