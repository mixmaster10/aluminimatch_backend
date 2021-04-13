<?php

use Illuminate\Database\Seeder;

class UserRelationshipSingleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
//            [
//                'uid' => 4,
//                'meet_divorced' => 0,
//                'single_scale' => 6,
//                'ethnicity' => 2,
//                'music' => 5,
//                'drink' => 1,
//                'privacy_drink' => 1,
//                'smoke' => 0,
//                'privacy_smoke' => 1,
//                'sex_scale' => 6,
//                'have_pets' => 1,
//                'pets' => 1,
//                'pets_scale' => 7,
//                'like_pets' => NULL,
//                'match_age' => 3,
//                'bodytype' => NULL,
//                'privacy_body_type' => 0,
//                'own_body_type' => 0,
//                'privacy_own_body_type' => 0,
//                'laugh' => 3,
//                'privacy_laugh' => 0,
//                'laugh_scale' => 6,
//                'married_before' => 0,
//                'married_count' => NULL,
//                'married_scale' => 10,
//                'have_kids' => 0,
//                'kids_scale' => NULL
//            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_relationship_single')->insert($item);
        }
    }
}
