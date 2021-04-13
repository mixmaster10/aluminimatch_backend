<?php

use Illuminate\Database\Seeder;

class UserSpeakLanguagesTableSeeder extends Seeder
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
                'language' => 0
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_speak_languages')->insert($item);
        }
    }
}
