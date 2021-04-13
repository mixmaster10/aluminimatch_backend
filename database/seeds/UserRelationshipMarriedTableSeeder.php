<?php

use Illuminate\Database\Seeder;

class UserRelationshipMarriedTableSeeder extends Seeder
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
                [
                    'uid' => 1,
                    'is_alumni' => 0,
                    'meet_couple_scale' => NULL,
                    'year' => 2,
                    'privacy_married_year' => NULL,
                    'have_kids' => 1,
                    'meet_kid_scale' => 5,
                    'meet_married_scale' => NULL,
                    'plan_marry_date' => NULL,
                    'finance' => NULL
                ]
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_relationship_married')->insert($item);
        }
    }
}
