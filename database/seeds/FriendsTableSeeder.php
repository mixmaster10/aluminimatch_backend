<?php

use Illuminate\Database\Seeder;

class FriendsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $friends = [
//            [
//                'uid' => 1,
//                'fid' => 2
//            ]
        ];

        foreach ($friends as $friend) {
            \App\Models\Friend::create($friend);
        }
    }
}
