<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use App\Models\UserDegree;
use App\Models\UserAthlete;
use App\Models\UserOrg;
use App\Models\UserHobby;
use App\Models\UserIndustry;
use DB;

trait ApiResponser{

	protected function dataOnlyResponse($id)
	{
		$user = User::where('id', $id)->withCount([
            'friends',
            'visits',
            'visits AS new_visits_count' => function ($query) {
                $query->where('count', '=', 1);
            }
        ])->with(['college', 'coordinate', 'graduated'])->first();

        $user->fr_count = DB::table('friend_requests')->where('fid', $user->id)->count();

        $query = User::where('id', '<>', $user->id)->where('college', $user->college)->whereNotNull(['verified_at', 'activated_at'])
            ->join('friends', 'friends.fid', '=', 'users.id')->where([['friends.uid', '=', $user->id], ['shared', '=', 1]])->select('users.*', 'friends.shared')
            ->with('graduated')->withCount(['friends as shares' => function ($query) {
                $query->where('shared', '=', 1);
            }]);
        $query = $this->buildNormalDistanceQuery($query, $user->coordinate);

        $friends = $query->get()->map(function ($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });

        $ps = [
            'degrees' => UserDegree::where('uid', $user->id)->with('degree')->with('ibc')->get(),
            'athlete' => UserAthlete::where('uid', $user->id)->with('athlete')->first(),
            'orgs' => UserOrg::where('uid', $user->id)->with('org')->get()
            
        ];

        $cl = [
            'gender_age' => DB::table('user_gender_ages')->where('uid', $user->id)->first(),
            'ethnicity' => DB::table('user_ethnicities')->where('uid', $user->id)->first(),
            'speak_languages' => DB::table('user_speak_languages')->where('uid', $user->id)->get(),
            'learn_languages' => DB::table('user_learn_languages')->where('uid', $user->id)->get(),
            'religion' => DB::table('user_religions')->where('uid', $user->id)->first(),
            'relationship' => DB::table('user_relationships')->where('uid', $user->id)->first(),
            'work' => DB::table('user_work_careers')->where('uid', $user->id)->first(),
            'industry' => UserIndustry::where('uid', $user->id)->with('industry')->get(),
            'home' => DB::table('user_homes')->where('uid', $user->id)->first(),
            'hometown' => DB::table('user_hometowns')->where('uid', $user->id)->first(),
            'hobbies' => UserHobby::where('uid', $user->id)->with('hobby')->get(),
            'causes' => DB::table('user_causes')->where('uid', $user->id)->get(),
            'school' => DB::table('user_schools')->where('uid', $user->id)->first(),
		];
		
		return response()->json(compact('user', 'friends', 'ps', 'cl'));
	}

    protected function successResponse($data, $message = null, $code = 200)
	{
		$response = [
			'status'=> 'Success', 
			'message' => $message,
		];
		if($data){
			$response = Arr::add($response, 'data', $data);
		}
		return response()->json($response, $code);
	}

	protected function errorResponse($message = null, $code)
	{
		$errorResponse = [
			'status'=>'Error',
			'message' => $message
		];
		return response()->json($errorResponse, $code);
	}


	function object_to_array( $object ) {
		if( !is_object( $object ) && !is_array( $object ) ) return $object;
			return array_map( 'object_to_array', (array) $object );
	}

}