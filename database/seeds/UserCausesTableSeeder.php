<?php

use Illuminate\Database\Seeder;

class UserCausesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $causes = [
            [
                'uid' => 1,
                'cause' => 6,
                'scale' => 8
            ]
        ];

        foreach ($causes as $cause) {
            \Illuminate\Support\Facades\DB::table('user_causes')->insert($cause);
        }
    }
}
