<?php

use Illuminate\Database\Seeder;

class UserRelationshipFoodsTableSeeder extends Seeder
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
                'food' => 4
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_relationship_foods')->insert($item);
        }
    }
}
