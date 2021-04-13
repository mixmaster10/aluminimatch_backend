<?php

use Illuminate\Database\Seeder;

class UserHobbiesTableSeeder extends Seeder
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
                'hobby' => 4,
                'skill_scale' => 5,
                'match_scale' => 5,
                'teach_scale' => NULL
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_hobbies')->insert($item);
        }
    }
}
