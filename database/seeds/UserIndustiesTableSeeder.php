<?php

use Illuminate\Database\Seeder;

class UserIndustiesTableSeeder extends Seeder
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
                'industry' => 1
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_industries')->insert($item);
        }
    }
}
