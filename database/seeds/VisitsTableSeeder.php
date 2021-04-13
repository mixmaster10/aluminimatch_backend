<?php

use Illuminate\Database\Seeder;

class VisitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $visits = [
//            [
//                'uid' => 1,
//                'vid' => 2,
//                'count' => 5
//            ]
        ];

        foreach ($visits as $visit) {
            \Illuminate\Support\Facades\DB::table('visits')->insert($visit);
        }
    }
}
