<?php

use Illuminate\Database\Seeder;

class UserAthletesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $athletes = [
            [
                'uid' => 1,
                'member' => 0,
                'athlete' => 2,
                'position' => NULL,
                'privacy' => 1
            ]
        ];

        foreach ($athletes as $athlete) {
            \App\Models\UserAthlete::create($athlete);
        }
    }
}
