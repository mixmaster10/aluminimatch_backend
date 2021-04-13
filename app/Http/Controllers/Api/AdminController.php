<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use App\Models\Admin;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Traits\ApiResponser;
use App\Traits\DistanceTrait;

class AdminController extends Controller
{
    use DistanceTrait;
    use ApiResponser;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if($validator->fails()){
            return error_response("email or password is missing.");
        }
        $user = Admin::where('email',$request->email)->first();
        if (is_null($user) || !Hash::check($request->password, $user->password)) {
            return error_response("Invalid email or password.");
        }
        $token = JWTAuth::fromUser($user);
        return success_response('login succeed..', compact('user','token'));
    }

    public function get_users(Request $request)
    {
        $id = $request->route('user_id');
        $tab = $request->route('tab');
        $post_id = $request->get('post_id');

        if(is_null($id)) {
            $users = User::with('college')->get();
            return success_response('users listed..', $users);
        }
        if($tab === "info") return $this->get_user_info($id);
        else if($tab === "friends") return $this->get_user_friends($id);
        else if($tab === "board") return $this->get_user_posts($id, $post_id);
        else return error_response('Invalid api request...');
    }

    private function get_user_info($user_id)
    {
        $user = $this->dataOnlyResponse($user_id);
        return success_response('user listed..', $user);
    }

    private function get_user_friends($user_id)
    {
        $user_friends = User::where('id', '<>', $user_id)
                        ->whereNotNull(['verified_at', 'activated_at'])
                        ->join('friends', 'friends.fid', '=', 'users.id')
                        ->where('friends.uid', '=', $user_id)
                        ->get(['users.id', 'first_name', 'last_name', 'avatar']);
        return success_response('user friends listed..', $user_friends);
    }

    private function get_user_posts($user_id, $post_id)
    {
        if($post_id){
            $post = Post::where("id",'=',$post_id)->where("userId",'=',$user_id)
            ->with(['type','category','comments'])->withCount(['likes','comments'])->first();
        } else {
            $post = Post::where("userId",'=',$user_id)
                ->with(['type','category','comments'])->withCount(['likes','comments'])->orderBy('created_at', 'desc')->get();
        }

        return success_response('user posts listed..', $post);
    }

}
