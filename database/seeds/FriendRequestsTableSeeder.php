<?php

use Illuminate\Database\Seeder;

class FriendRequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $friend_requests = [
//            [
//                'uid' => 1,
//                'fid' => 2,
//                'msg' => 'Hello world!'
//            ]
        ];

        foreach ($friend_requests as $request) {
            \Illuminate\Support\Facades\DB::table('friend_requests')->insert($request);
        }
    }
}
