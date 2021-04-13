<?php

use Illuminate\Database\Seeder;

class UserLoginsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_logins = [
            [
                'uid' => 1,
                'count' => 2450
            ]
        ];

        foreach ($user_logins as $login) {
            \Illuminate\Support\Facades\DB::table('user_logins')->insert($login);
        }
    }
}
