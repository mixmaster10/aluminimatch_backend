<?php

use Illuminate\Database\Seeder;

class UserHomesTableSeeder extends Seeder
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
                'country' => 'United State',
                'state' => 'Washington',
                'zip' => '20001',
                'scale' => NULL,
                'game_scale' => NULL,
                'event_scale' => NULL,
                'privacy' => NULL
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_homes')->insert($item);
        }
    }
}
