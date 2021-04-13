<?php

use Illuminate\Database\Seeder;

class UserRelationshipKidsTableSeeder extends Seeder
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
                'gender' => 0,
                'age' => 0
            ],
            [
                'uid' => 1,
                'gender' => 0,
                'age' => 1
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_relationship_kids')->insert($item);
        }
    }
}
