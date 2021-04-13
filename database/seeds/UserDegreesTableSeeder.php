<?php

use Illuminate\Database\Seeder;

class UserDegreesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $degrees = [
            [
                'uid' => 1,
                'type' => 1,
                'degree' => 1,
                'ibc' => 1,
                'year' => 2000
            ]
        ];

        foreach ($degrees as $degree) {
            \App\Models\UserDegree::create($degree);
        }
    }
}
