<?php

use Illuminate\Database\Seeder;

class UserLearnLanguageScalesTableSeeder extends Seeder
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
                'fluent' => 5,
                'level' => 5,
                'tutor' => 5,
                'teach' => 5
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_learn_language_scales')->insert($item);
        }
    }
}
