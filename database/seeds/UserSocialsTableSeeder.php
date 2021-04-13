<?php

use Illuminate\Database\Seeder;

class UserSocialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $connects = [
            [
                'uid' => 1,
                'twitter' => 'alumnimatchco'
            ]
        ];

        foreach ($connects as $connect){
            \App\Models\UserSocial::create($connect);
        }
    }
}
