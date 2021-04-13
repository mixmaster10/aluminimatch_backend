<?php

use Illuminate\Database\Seeder;

class UserGenderAgesTableSeeder extends Seeder
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
                'gender' => 0,
                'age' => 0
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_gender_ages')->insert($item);
        }
    }
}
