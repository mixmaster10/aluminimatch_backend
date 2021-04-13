<?php

use Illuminate\Database\Seeder;

class UserOrgsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orgs = [
            [
                'uid' => 1,
                'org' => 1
            ]
        ];

        foreach ($orgs as $org) {
            \App\Models\UserOrg::create($org);
        }
    }
}
