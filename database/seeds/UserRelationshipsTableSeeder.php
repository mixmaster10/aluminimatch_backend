<?php

use Illuminate\Database\Seeder;

class UserRelationshipsTableSeeder extends Seeder
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
                'relationship' => 0
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_relationships')->insert($item);
        }
    }
}
