<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Models\Friend;
use App\Models\Message;
use App\Models\User;
use App\Models\UserCoords;
use App\Traits\DistanceTrait;
use App\Traits\FriendTrait;
use App\Traits\MatchTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    use MatchTrait;
    use FriendTrait;
    use DistanceTrait;

    public function index(Request $request) {
        $user = $request->get('user');
        $messages = Message::whereIn('id', function($query) use ($user) {
            $query->selectRaw('max(`id`)')
                ->from('messages')
                ->where('rid', '=', $user->id)
                ->orWhere(function($query1) use($user){
                    return $query1->where('sid', $user->id)->whereNotIn('rid', function($query2) use($user){
                        $query2->selectRaw('sid')
                            ->from('messages')
                            ->where('rid', $user->id)
                            ->groupBy('sid');
                    });
                })
                ->groupBy('sid');
            })
            ->orderBy('created_at', 'desc')
            ->with([
                'sender' => function($query) use ($user) {
                    $query->where('users.id', '<>', $user->id)->with('graduated');
                },
                'receiver' => function($query) use ($user) {
                    $query->where('id', '<>', $user->id)->with('graduated');
                }
            ])
            ->get()
            ->map(function($message) use($user){
                if (!is_null($message->sender) && $message->sender->id !== $user->id) {
                    $message->sender->match = $this->getMatchPercent($user->id, $message->sender->id);
                    $message->unread_count = Message::where('sid', $message->sender->id)->where('rid', $user->id)->where('read', 0)->count();
                }
                if (!is_null($message->receiver) && $message->receiver->id !== $user->id) {
                    $message->receiver->match = $this->getMatchPercent($user->id, $message->receiver->id);
                    $message->unread_count = Message::where('sid', $user->id)->where('rid', $message->receiver->id)->where('read', 0)->count();
                }
                return $message;
            });
        $user->unread_num = Message::where('rid', $user->id)->where('read', 0)->count();
        return response()->json(compact('messages', 'user'));
    }


    public function getUserMessages(Request $request, $uid) {
        $user = $request->get('user');
        $messages = Message::where([['sid', '=', $user->id], ['rid', '=', $uid]])->orWhere([['sid', '=', $uid], ['rid', '=', $user->id]])
            ->orderBy('updated_at', 'desc')->skip($request->query('count'))->take(10)->get();
        return response()->json(compact('messages'));
    }

    public function sendMessage(Request $request) {
        $user = $request->get('user');
//        $is_friend = Friend::where([['uid', '=', $user->id], ['fid', '=', $request['rid']]])->exists();
//        if (!$is_friend) {
//            $is_request = DB::table('friend_requests')->where([['uid', '=', $request['rid']], ['fid', '=', $user->id]])->exists();
//            if ($is_request) {
//                $this->approve($user->id, $request['rid']);
//            } else {
//                $is_pending = DB::table('friend_requests')->where([['uid', '=', $user->id], ['fid', '=', $request['rid']]])->exists();
//                if (!$is_pending) {
//                    $this->invite($user->id, $request['rid'], $request['title'].'\n'.$request['content']);
//                }
//                return response()->json(['message' => 'You can\'t send message to this user until he accept your friend request'], 403);
//            }
//        }
        $message = Message::create([
            'sid' => $user->id,
            'rid' => $request['rid'],
            'title' => $request['title'],
            'content' => $request['content']
        ]);

        $data = [
            'uid' => $user->id,
            'message' => $message,
            'type' => 'user',
        ];
        event(new MessageSent($data));
        return response()->json($message);
    }

    public function markAsRead($mid) {
        Message::where('id', $mid)->update(['read' => 1]);
        return response()->json(true);
    }

    public function deleteMessage($mid) {
        Message::where('id', $mid)->delete();
        return response()->json(true);
    }

    public function sendMessageToAll(Request $request) {
        $user = $request->get('user');
        $messages = Friend::where('uid', $user->id)->select('fid')->get()->map(function ($friend) use ($user, $request) {
            $message = Message::create([
                'sid' => $user->id,
                'rid' => $friend->fid,
                'title' => $request['title'],
                'content' => $request['content']
            ]);
            return $message;
        });

        $data = [
            'uid' => $user->id,
            'messages' => $messages,
            'type' => 'multi',
        ];
        event(new MessageSent($data));
        return response()->json(true);
    }

    public function sendMessageInRadius(Request $request) {
        $user = $request->get('user');
        $coords = UserCoords::where('uid', $user->id)->first();
        if (!is_null($coords)) {
            $query = User::whereNotNull(['verified_at', 'activated_at'])->where('college', $user->college)->where('id', '<>', $user->id)
                ->join('user_coords', 'users.id', '=', 'user_coords.uid')->where('user_coords.show', '=', 1)
                ->join('friends', 'friends.uid', '=', 'users.id')->where('friends.fid', '=', $user->id);
            $messages = $this->buildDistanceQuery($query, $coords, $request['radius'])->selectRaw('users.*')->get()->map(function($near) use ($user, $request) {
                $message = Message::create([
                    'sid' => $user->id,
                    'rid' => $near->id,
                    'title' => $request['title'],
                    'content' => $request['content']
                ]);
                return $message;
            });

            $data = [
                'uid' => $user->id,
                'messages' => $messages,
                'type' => 'multi',
            ];
            event(new MessageSent($data));
        }
        return response()->json(true);
    }

    public function sendMessageToUsers(Request $request) {
        $user = $request->get('user');
        $messages = array();
        for ($i = 0; $i < count($request['receiveIds']); $i++) {
            $message = Message::create([
                'sid' => $user->id,
                'rid' => $request['receiveIds'][$i],
                'title' => $request['title'],
                'content' => $request['content']
            ]);
            array_push($messages, $message);
        }

        $data = [
            'uid' => $user->id,
            'messages' => $messages,
            'type' => 'multi',
        ];
        event(new MessageSent($data));
        return response()->json(true);
    }

    public function deleteAllMessagesByUID(Request $request) {
        $uid = $request->user()->id;

        Message::where('sid','=',$uid)->delete();
        return response()->json(true);
    }

    public function sendPush(Request $request) {

        $data = [
            'uid' => $request['uid'],
            'rid' => $request['rid'],
            'type' => 'user',
            'title' => $request['title']
        ];
        event(new MessageSent($data));
        return response()->json(true);
    }
}
