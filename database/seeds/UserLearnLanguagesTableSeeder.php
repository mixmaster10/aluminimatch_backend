<?php

use Illuminate\Database\Seeder;

class UserLearnLanguagesTableSeeder extends Seeder
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
                'language' => 2,
                'fluent' => 5
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_learn_languages')->insert($item);
        }
    }
}
