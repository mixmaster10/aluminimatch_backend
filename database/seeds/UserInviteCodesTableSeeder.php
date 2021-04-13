<?php

use Illuminate\Database\Seeder;

class UserInviteCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_codes = [
            [
                'uid' => 1,
                'code' => 123456
            ]
        ];

        foreach ($user_codes as $code) {
            \Illuminate\Support\Facades\DB::table('user_invite_codes')->updateOrInsert(
                [
                    'uid' => $code['uid']
                ],
                [
                    'code' => $code['code'],
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]
            );
        }
    }
}
