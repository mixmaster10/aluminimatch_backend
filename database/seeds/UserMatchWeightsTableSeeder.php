<?php

use Illuminate\Database\Seeder;

class UserMatchWeightsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'uid' => 1,
                'ps' => 50.00,
                'cl' => 50.00
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_match_weights')->insert($item);
        }
    }
}
