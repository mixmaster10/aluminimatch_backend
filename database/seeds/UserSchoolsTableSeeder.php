<?php

use Illuminate\Database\Seeder;

class UserSchoolsTableSeeder extends Seeder
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
                'member' => 1,
                'satis_level' => 0
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_schools')->insert($item);
        }
    }
}
