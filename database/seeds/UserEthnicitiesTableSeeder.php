<?php

use Illuminate\Database\Seeder;

class UserEthnicitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ethnicities = [
            [
                'uid' => 1,
                'ethnicity' => 0,
                'privacy' => 0
            ]
        ];

        foreach ($ethnicities as $ethnicity) {
            \Illuminate\Support\Facades\DB::table('user_ethnicities')->insert($ethnicity);
        }

    }
}
