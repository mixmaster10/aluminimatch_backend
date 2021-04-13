<?php

use Illuminate\Database\Seeder;

class AthletesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $athletes = [
            ['id'=>1, 'name'=>'Baseball mens'],
            ['id'=>2, 'name'=>'Basketball mens'],
            ['id'=>3, 'name'=>'Cross Country mens'],
            ['id'=>4, 'name'=>'Fencing mens'],
            ['id'=>5, 'name'=>'Football mens'],
            ['id'=>6, 'name'=>'Golf mens'],
            ['id'=>7, 'name'=>'Gymnastics mens'],
            ['id'=>8, 'name'=>'Ice Hockey mens'],
            ['id'=>9, 'name'=>'Lacrosse mens'],
            ['id'=>10, 'name'=>'Rifle mens'],
            ['id'=>11, 'name'=>'Skiing mens'],
            ['id'=>12, 'name'=>'Soccer mens'],
            ['id'=>13, 'name'=>'Swimming & Diving mens'],
            ['id'=>14, 'name'=>'Tennis mens'],
            ['id'=>15, 'name'=>'Track & Field Indoor mens'],
            ['id'=>16, 'name'=>'Track & Field Outdoor mens'],
            ['id'=>17, 'name'=>'Volleyball mens'],
            ['id'=>18, 'name'=>'Water Polo mens'],
            ['id'=>19, 'name'=>'Wrestling mens'],
            ['id'=>20, 'name'=>'Basketball women'],
            ['id'=>21, 'name'=>'Beach Volleyball womens'],
            ['id'=>22, 'name'=>'Bowling womens'],
            ['id'=>23, 'name'=>'Cross Country womens'],
            ['id'=>24, 'name'=>'Fencing womens'],
            ['id'=>25, 'name'=>'Field Hockey womens'],
            ['id'=>26, 'name'=>'Golf womens'],
            ['id'=>27, 'name'=>'Gymnastics womens'],
            ['id'=>28, 'name'=>'Ice Hockey womens'],
            ['id'=>29, 'name'=>'Lacrosse womens'],
            ['id'=>30, 'name'=>'Rifle womens'],
            ['id'=>31, 'name'=>'Rowing woman'],
            ['id'=>32, 'name'=>'Skiing womens'],
            ['id'=>33, 'name'=>'Soccer womens'],
            ['id'=>34, 'name'=>'Softball womens'],
            ['id'=>35, 'name'=>'Swimming & Diving womens'],
            ['id'=>36, 'name'=>'Tennis womens'],
            ['id'=>37, 'name'=>'Track & Field Indoor womens'],
            ['id'=>38, 'name'=>'Track & Field Outdoor womens'],
            ['id'=>39, 'name'=>'Volleyball womens'],
            ['id'=>40, 'name'=>'Water Polo womens'],
        ];

        foreach ($athletes as $athlete){
            \App\Models\Athlete::create($athlete);
        }
    }
}
