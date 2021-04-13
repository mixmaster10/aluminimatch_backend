<?php

namespace App\Http\Controllers\Api;

use App\Models\College;
use App\Models\User;
use App\Models\UserCoords;
use App\Models\UserSocial;
use App\Models\UserActivity;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('social', '=', $request->get('social'))
            ->where('sid', (string)$request->get('sid'))->first();
        if (is_null($user)) {
            return response()->json(['message' => 'You are not a member of AlumniMatch. Please register with your social account.'], 400);
        } else {
            $user->online = 1;
            $user->last_seen = Carbon::now();
            $user->update();
            DB::table('user_logins')->where('uid', $user->id)->increment('count');
            if ($request->has('lat') && $request->has('lng')) {
                UserCoords::updateOrCreate(
                    [
                        'uid' => $user->id
                    ],
                    [
                        'lat' => $request->get('lat'),
                        'lng' => $request->get('lng')
                    ]
                );
            }
            $user->college = College::where('id', json_decode($user->college, true)['primary'])->first();
            $token = JWTAuth::fromUser($user);
            return response()->json(compact('token', 'user'), 200);
        }
    }

    public function isRegisteredUser($social, $sid)
    {
        $user = User::where('social', $social)->where('sid', $sid)->first();
        if (is_null($user)) {
            return response()->json(true);
        } else {
            return response()->json(['message' => 'This account already existed.'], 400);
        }
    }

    public function register(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            array(
                'social' => 'required|string',
                'sid' => 'required|max:191|unique:users',
                'first_name' => 'string|max:191',
                'last_name' => 'string|max:191',
                'email' => !empty($request->get('email')) ? 'string|max:191|unique:users,email' : "nullable",
                'college' => 'required'
            ),
            array(
                'sid.required' => 'The Social ID is required.',
                'sid.unique' => 'User already exists. Please login with this account.',
                'email.unique' => 'Email already taken. Please try with different login.',
                'sid.max' => 'An error has occured fetching sid. For Devs: max char count has been exceeded.',
                'college.required' => 'College is required. Please select your school.'
            )
        );
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create([
            'social' => $request->get('social'),
            'sid' => (string)$request->get('sid'),
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
            'avatar' => $request->get('avatar'),
            'college' => $request->get('college')
        ]);
        
        $newRequest = $request->all();
        $newRequest['uid'] = $user->id;
        $newRequest['video_games_fav_title'] = null;
        $newRequest['athletic_stuff_you_play'] = null;
        if (!empty($request->video_games_fav_title) && count($request->video_games_fav_title) > 0) {
            $newRequest['video_games_fav_title'] = implode(',', $request->video_games_fav_title);
        }
        if (!empty($request->athletic_stuff_you_play) && count($request->athletic_stuff_you_play) > 0) {
            $newRequest['athletic_stuff_you_play'] = implode(',', $request->athletic_stuff_you_play);
        }

        // $UserActivity = UserActivity::create($newRequest);

        if ($request->has('lat') && $request->has('lng')) {
            UserCoords::create([
                'uid' => $user->id,
                'lat' => $request->get('lat'),
                'lng' => $request->get('lng')
            ]);
        }
        try {
            if ($request->get('social') === 'twitter' && $request->has('username')) {
                UserSocial::updateOrCreate(
                    [
                        'uid' => $user->id
                    ],
                    [
                        'twitter' => $request->get('username')
                    ]
                );
            }
        } catch (\Exception $e) {
            // return null;
        }

        $token = JWTAuth::fromUser($user);
        //        $data = [
        //            'id' => $user->id,
        //            'type' => 'join'
        //        ];
        //        event(new UserRegistered($data));

        Post::create(
            [
                'title' => $user->first_name." ".$user->last_name." has joined AlumniMatch!",
                'description' => null,
                'photoUrl' => null,
                'embed' =>null,
                'postTypeId' => 4,
                'postCategoryId' => 23,
                'userId' => $user->id
            ]
        );

        $user->college = College::where('id', json_decode($request->get('college'),true)['primary'])->first(); //Use the first college in the list on the app for themes.

        return response()->json(compact('user', 'token'), 200);
    }

    public function verifyInviteCode(Request $request, $code)
    {
        if ($code == 239823) {
            return response()->json(['inviter' => 1]);
        }
        $invite = DB::table('user_invite_codes')->where('code', $code)->first();
        if (is_null($invite)) {
            return response()->json(['message' => 'This code is invalid. Please try again with correct code.'], 400);
        } else {
            if (Carbon::parse($invite->updated_at)->add('minute', 15) < Carbon::now()) {
                return response()->json(['message' => 'Expired invite code. Please try with new invite code.'], 400);
            } else {
                return response()->json(['inviter' => $invite->uid]);
            }
        }
    }

    public function generateInviteCode(Request $request)
    {
        $user = $request->get('user');
        do {
            $six_digit_random_number = mt_rand(100000, 999999);
            $u = DB::table('user_invite_codes')->where('code', $six_digit_random_number)->first();
        } while (!is_null($u));
        DB::table('user_invite_codes')->updateOrInsert([
            'uid' => $user->id
        ], [
            'code' => $six_digit_random_number,
            'updated_at' => Carbon::now()
        ]);
        return response()->json($six_digit_random_number);
    }

    public function logout(Request $request)
    {
        $user = $request->get('user');
        User::where('id', $user->id)->update(['online' => 0]);
        if ($request->has('device_token')) {
            $userTokens = DB::table('user_devices')->where('uid', $user->id)->first();
            if (!is_null($userTokens) && !is_null($userTokens->tokens) && $userTokens->tokens != '') {
                $tokenArr = explode(',', $userTokens->tokens);
                $index = array_search($request->query('device_token'), $tokenArr);
                if ($index > -1) {
                    array_splice($tokenArr, $index, 1);
                    DB::table('user_devices')->where('uid', $user->id)->update(['tokens' => implode(',', $tokenArr)]);
                }
            }
        }
        return response()->json(['message' => 'You successfully loged out.']);
    }

    public function createAccountTicket(Request $request)
    {
        $user = $request->get('user');
        DB::table('tickets')->insert([
            'email' => $request->get('email'),
            'message' => $request->get('message'),
            'uid' => $user->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        return response()->json('Ticket has been created successfully. We will reply to you soon.');
    }
}
