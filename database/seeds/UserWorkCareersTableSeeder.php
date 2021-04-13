<?php

use Illuminate\Database\Seeder;

class UserWorkCareersTableSeeder extends Seeder
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
                'work_for' => 3,
                'privacy_business_city' => 0,
                'privacy_travel_city' => 0,
                'employment_status' => 3,
                'work_title' => 0,
                'work_title_scale' => NULL,
                'hire_full' => 1,
                'hire_full_count' => 0,
                'hire_full_looking' => 0,
                'hire_full_for' => 0,
                'privacy_hire_full' => 0,
                'hire_gig' => NULL,
                'hire_gig_count' => NULL,
                'privacy_hire_gig' => 0,
                'hire_intern' => NULL,
                'hire_intern_count' => NULL,
                'hire_intern_looking' => NULL,
                'hire_intern_for' => NULL,
                'privacy_hire_intern' => 0,
                'own_business' => 0,
                'seeking_investment' => 1,
                'buying_stuff' => 1,
                'customer' => 0,
                'investor' => true,
                'wealth' => 3,
                'wealth_scale' => 5,
                'review_plan' => 3,
                'privacy_investor' => true
            ]
        ];

        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('user_work_careers')->insert($item);
        }
    }
}
