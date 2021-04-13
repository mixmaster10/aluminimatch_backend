<?php

use Illuminate\Database\Seeder;

class UserHealthsTableSeeder extends Seeder
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
                'mental' => 0,
                'mental_privacy' => NULL,
                'physical' => 0,
                'physical_privacy' => NULL
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_healths')->insert($item);
        }
    }
}
