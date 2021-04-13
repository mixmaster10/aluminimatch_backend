<?php

use App\Models\AccessRequest as ModelsAccessRequest;
use Illuminate\Database\Seeder;

class AccessRequest extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allRequests=[
            [
             'id'=> 1,
            'uid'=> 1,
            'requested_user'=> 2,
            'access'=> "age",
            'approve'=> "false",
            ],
            [
                'id'=> 2,
               'uid'=> 6,
               'requested_user'=> 12,
               'access'=> "health",
               'approve'=> "false",
            ],
            [
                'id'=> 3,
               'uid'=> 90,
               'requested_user'=> 42,
               'access'=> "home",
               'approve'=> "false",
            ],
            [
                'id'=> 4,
               'uid'=> 90,
               'requested_user'=> 3244,
               'access'=> "relation",
               'approve'=> "false",
            ],  [
                'id'=> 5,
               'uid'=> 23,
               'requested_user'=> 324,
               'access'=> "age",
               'approve'=> "false",
            ],
            [
                'id'=> 6,
               'uid'=> 52,
               'requested_user'=> 40,
               'access'=> "age",
               'approve'=> "false",
            ],
            [
                'id'=> 7,
               'uid'=> 54,
               'requested_user'=> 234,
               'access'=> "age",
               'approve'=> "false",
            ],  [
                'id'=> 8,
               'uid'=> 90,
               'requested_user'=> 49,
               'access'=> "age",
               'approve'=> "false",
            ],
            [
                'id'=> 9,
               'uid'=> 81,
               'requested_user'=> 13,
               'access'=> "health",
               'approve'=> "false",
               ]
            ];
            foreach ($allRequests as $request){
                ModelsAccessRequest::create($request);
            }
    }
}
