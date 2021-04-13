<?php

use Illuminate\Database\Seeder;

class UserReligionsTableSeeder extends Seeder
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
                'religion' => 0,
                'church' => NULL,
                'year' => NULL
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_religions')->insert($item);
        }
    }
}
