<?php

use Illuminate\Database\Seeder;

class HobbiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hobbies = [
            [
                'id' => 1,
                'name' => 'Cooking'
            ],
            [
                'id' => 2,
                'name' => 'Exercise'
            ],
            [
                'id' => 3,
                'name' => 'Hiking'
            ],
            [
                'id' => 4,
                'name' => 'Animal care'
            ],
            [
                'id' => 5,
                'name' => 'Camping'
            ],
            [
                'id' => 6,
                'name' => 'Boating'
            ],
            [
                'id' => 7,
                'name' => 'Sky Diving'
            ],
            [
                'id' => 8,
                'name' => 'Shopping'
            ],
            [
                'id' => 9,
                'name' => 'Volunteer Work'
            ],
            [
                'id' => 10,
                'name' => 'Theater'
            ],
            [
                'id' => 11,
                'name' => 'Photography'
            ],
            [
                'id' => 12,
                'name' => 'Travel'
            ],
            [
                'id' => 13,
                'name' => 'Craft beer'
            ],
            [
                'id' => 14,
                'name' => 'Wine'
            ],
            [
                'id' => 15,
                'name' => 'Hunting'
            ],
            [
                'id' => 16,
                'name' => 'Archery'
            ],
            [
                'id' => 17,
                'name' => 'Fishing'
            ],
            [
                'id' => 18,
                'name' => 'Tatoos'
            ],
            [
                'id' => 19,
                'name' => 'Astronomy'
            ],
            [
                'id' => 20,
                'name' => 'Wood working'
            ],
            [
                'id' => 21,
                'name' => 'Gardening'
            ],
            [
                'id' => 22,
                'name' => 'Watching Movies'
            ],
            [
                'id' => 23,
                'name' => 'Watching Sports'
            ],
            [
                'id' => 24,
                'name' => 'Concerts'
            ],
            [
                'id' => 25,
                'name' => 'Church Activities'
            ],
            [
                'id' => 26,
                'name' => 'Family'
            ],
            [
                'id' => 27,
                'name' => 'Sewing'
            ],
            [
                'id' => 28,
                'name' => 'Crafts'
            ],
            [
                'id' => 29,
                'name' => 'Drawing'
            ],
            [
                'id' => 30,
                'name' => 'Painting'
            ],
            [
                'id' => 31,
                'name' => 'Reading'
            ],
            [
                'id' => 32,
                'name' => 'Writing'
            ],
            [
                'id' => 33,
                'name' => 'Car mechanics'
            ],
            [
                'id' => 34,
                'name' => 'Motorcycle mechanics'
            ],
            [
                'id' => 35,
                'name' => '3D printing'
            ],
            [
                'id' => 36,
                'name' => 'Carpentry'
            ],
            [
                'id' => 37,
                'name' => 'Woodworking'
            ],
            [
                'id' => 38,
                'name' => 'RC vehicles'
            ]
        ];

        foreach ($hobbies as $hobby) {
            \App\Models\Hobby::create($hobby);
        }
    }
}
