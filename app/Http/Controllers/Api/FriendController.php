<?php

namespace App\Http\Controllers\Api;

use App\Events\FriendApproved;
use App\Events\FriendRequested;
use App\Models\Friend;
use App\Models\User;
use App\Models\UserCoords;
use App\Traits\DistanceTrait;
use App\Traits\FriendTrait;
use App\Traits\MatchTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FriendController extends Controller
{
    use FriendTrait;
    use DistanceTrait;
    use MatchTrait;

    public function approveFriendRequest(Request $request) {
        $user = $request->get('user');
        $this->approve($user->id, $request['fid']);
        $data = [
            'uid' => $user->id,
            'fid' => $request['fid'],
        ];
        event(new FriendApproved($data));
        return response()->json(true);
    }

    public function ignoreFriendRequest(Request $request) {
        $user = $request->get('user');
        $this->ignore($user->id, $request['fid']);
        return response()->json(true);
    }

    public function inviteAsFriend(Request $request) {
        $user = $request->get('user');
        $this->invite($user->id, $request['fid'], $request['msg']);
        $data = [
            'uid' => $user->id,
            'fid' => $request['fid'],
            'msg' => $request['msg']
        ];
        event(new FriendRequested($data));
        return response()->json(true);
    }

    public function getAllFriends(Request $request) {
        $user = $request->get('user');
        $coords = UserCoords::where('uid', $user->id)->first();
        $query = User::where('id', '<>', $user->id)->where('college', $user->college)->whereNotNull(['verified_at', 'activated_at'])
            ->join('friends', 'friends.fid', '=', 'users.id')->where('friends.uid', '=', $user->id)->select('users.*', 'friends.shared')
            ->with('graduated')->withCount(['friends as shares' => function($query) {$query->where('shared', '=', 1);}]);
        $query = $this->buildNormalDistanceQuery($query, $coords);

        $users = $query->get()->map(function ($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });

        return response()->json($users->reverse()->values());
    }

}
