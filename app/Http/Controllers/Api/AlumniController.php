<?php

namespace App\Http\Controllers\Api;

use App\Models\College;
use App\Models\Friend;
use App\Models\Message;
use App\Models\User;
use App\Models\UserAthlete;
use App\Models\UserCoords;
use App\Models\UserDegree;
use App\Models\UserHobby;
use App\Models\UserOrg;
use App\Models\UserIndustry;
use App\Models\Visit;
use App\Models\Post;
use App\Traits\DistanceTrait;
use App\Traits\MatchTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\FriendTrait;
use App\Events\BlockedUser;
use App\Traits\ApiResponser;

class AlumniController extends Controller
{
    use FriendTrait;
    use MatchTrait;
    use DistanceTrait;

    public function index(Request $request)
    {
        $user = $request->get('user');
        $colleges = json_decode($user->college, true);
        $users = User::whereNotNull(['verified_at', 'activated_at'])
            ->where('id', '<>', $user->id)
            ->whereJsonContains('college',$colleges["primary"])
            ->orderBy('updated_at', 'desc')->take(100)->get()
            ->map(function ($alumni) use ($user) {
                $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
                $alumni->distance = $this->getDistance($user->id, $alumni->id);
                $alumni->shares = Friend::where('uid', $alumni->id)->where('shared', 1)->count();
                return $alumni;
            });
        return response()->json($users);
    }

    public function getUserDetail(Request $request, $uid)
    {
        $user = $request->get('user');
        $percents = $this->getMatchPercent($user->id, $uid);
        return response()->json(compact('percents'));
    }

    public function getDashboardData(Request $request)
    {
        $user = $request->get('user');
        $colleges = json_decode($user->college, true);

        $messages = Message::with('sender')
            ->where('rid', $user->id)
            ->where('read', 0)
            ->orderBy('updated_at', 'desc')
            ->take(5)->get();

        $visitors = Visit::where('vid', $user->id)
            ->join('users', 'visits.uid', '=', 'users.id')
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.avatar', 'users.online', 'visits.count', 'visits.updated_at')
            ->orderBy('updated_at', 'desc')->take(20)->get();
        // $nears = [];
        $coords = UserCoords::where('uid', $user->id)->first();
        if (!is_null($coords)) {
            $query = User::whereNotNull(['verified_at', 'activated_at'])->whereJsonContains('college',$colleges["primary"])->where('id', '<>', $user->id)->join('user_coords', 'users.id', '=', 'user_coords.uid')->where('user_coords.show', '=', 1);
            $nears = $this->buildDistanceQuery($query, $coords, $coords->radius)->selectRaw('users.*')->with('coordinate')->get();
        } else {
            $query = User::whereNotNull(['verified_at', 'activated_at'])->whereJsonContains('college',$colleges["primary"])->where('id', '<>', $user->id)->join('user_coords', 'users.id', '=', 'user_coords.uid')->where('user_coords.show', '=', 1);
            $nears = [];
        }

        $friend_requests = DB::table('friend_requests')->where('fid', $user->id)
            ->join('users', 'users.id', '=', 'friend_requests.uid')
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.avatar', 'users.online', 'friend_requests.msg', 'friend_requests.updated_at')
            ->orderBy('updated_at', 'desc')->take(20)->get();

        $matchArr = array();
        foreach ($messages as $message) {
            if (array_key_exists($message->sender->id, $matchArr)) {
                $message->sender->match = $matchArr[$message->sender->id];
            } else {
                $match = $this->getMatchPercent($user->id, $message->sender->id);
                $matchArr[$message->sender->id] = $match;
                $message->sender->match = $match;
            }
        }
        foreach ($visitors as $v) {
            if (array_key_exists($v->id, $matchArr)) {
                $v->match = $matchArr[$v->id];
            } else {
                $match = $this->getMatchPercent($user->id, $v->id);
                $matchArr[$v->id] = $match;
                $v->match = $match;
            }
        }
        foreach ($nears as $near) {
            if (array_key_exists($near->id, $matchArr)) {
                $near->match = $matchArr[$near->id];
            } else {
                $match = $this->getMatchPercent($user->id, $near->id);
                $matchArr[$near->id] = $match;
                $near->match = $match;
            }
        }
        foreach ($friend_requests as $f) {
            if (array_key_exists($f->id, $matchArr)) {
                $f->match = $matchArr[$f->id];
            } else {
                $match = $this->getMatchPercent($user->id, $f->id);
                $matchArr[$f->id] = $match;
                $f->match = $match;
            }
        }

        $user = User::where('id', $user->id)->withCount([
            'messages' => function ($query) {
                $query->where('read', '=', 0);
            },
            'friends',
            'visits',
            'visits AS new_visits_count' => function ($query) {
                $query->where('count', '=', 1);
            }
        ])->with('coordinate')->first();


        $ranks = User::whereNotNull(['verified_at', 'activated_at'])->whereJsonContains('college',$colleges['primary'])->select('id')->withCount(['friends', 'visits'])->get()
        ->sort(function ($a, $b) {
            if ($a->friends_count == $b->friends_count) {
                return $a->visits_count < $b->visits_count;
            } else {
                return $a->friends_count < $b->friends_count;
            }
        })->toArray();
        $user->rank = array_search($user->id, array_column($ranks, 'id')) + 1;

        $user->freq_count = DB::table('friend_requests')->where('fid', $user->id)->count();

        return response()->json(compact('user', 'friend_requests', 'messages', 'nears', 'visitors'));
    }

    public function getNears(Request $request)
    {
        $user = $request->get('user');
        $colleges = json_decode($user->college, true);
        $coords = UserCoords::where('uid', $user->id)->first();
        if (!is_null($coords)) {
            $query = User::whereNotNull(['verified_at', 'activated_at'])
                ->with('graduated', 'coordinate')
                ->withCount(['friends as shares' => function ($query) {
                    $query->where('shared', '=', 1);
                }])
                ->whereJsonContains('college',$colleges["primary"])
                ->where('id', '<>', $user->id)
                ->join('user_coords', 'users.id', '=', 'user_coords.uid')->where('user_coords.show', '=', 1);
            $nears = $this->buildDistanceQuery($query, $coords, $coords->radius)->selectRaw('users.*')->get()
                ->map(function ($alumni) use ($user) {
                    $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
                    return $alumni;
                });
        }
        return response()->json($nears);
    }

    public function getLeaderboardData(Request $request)
    {
        $user = $request->get('user');
        $coords = UserCoords::where('uid', $user->id)->first();
        $colleges = json_decode($user->college, true);
        $ranks = User::whereNotNull(['verified_at', 'activated_at'])->withCount(['friends', 'visits'])->get()
        ->map(function($alumni) use ($colleges){
            $alumni->college = json_decode($alumni->college,true)['primary'];
            foreach($colleges as $college) {
                if($alumni->college == $college)
                    return $alumni;
            }
            unset($alumni);
        })->filter()->flatten(1)
        ->sort(function ($a, $b) {
            if ($a->friends_count == $b->friends_count) {
                return $a->visits_count < $b->visits_count;
            } else {
                return $a->friends_count < $b->friends_count;
            }
        })->toArray();
        $ranks = array_values($ranks);
        $user->rank = array_search($user->id, array_column($ranks, 'id')) + 1;

        $query = User::whereNotNull(['verified_at', 'activated_at'])->with('graduated')
            ->withCount(['friends as shares' => function ($query) {
                $query->where('shared', '=', 1);
            }]);
        
        
            //TODO: Coordinates should be optional, not required.
        $users = $this->buildNormalDistanceQuery($query, $coords)->get()->map(function ($u) use ($ranks, $user, $colleges) {
            $u->rank = array_search($u->id, array_column($ranks, 'id')) + 1;
            $u->match = $this->getMatchPercent($user->id, $u->id);
            $acol = json_decode($u->college,true)['primary'];
            $u->college = College::where('id', $acol)->first();
            foreach($colleges as $college) {
                if($acol == $college)
                    return $u;  
            }
            return null;
        });

        $user->college = College::where('id', json_decode($user->college,true)['primary'])->first();
        $users = $users->filter()->flatten(1);
        return response()->json(compact('user', 'users'));
    }

    public function getUsers(Request $request)
    {
        $user = $request->get('user');
        $coords = UserCoords::where('uid', $user->id)->first();
        $colleges = json_decode($user->college, true);
        $query = User::where('id', '<>', $user->id)->whereNotNull(['verified_at', 'activated_at'])->with('graduated')
        ->withCount(['friends as shares' => function ($query) {
            $query->where('shared', '=', 1);
        }]);
        if($coords)
            $query = $this->buildNormalDistanceQuery($query, $coords);

        $users = $query->orderBy('created_at', 'desc')->skip($request->query('count'))->take(20)->get()->map(function ($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });
        if(!$users)
            return response()->json(['message' => "User Data Error"],500);

        $users = $users->map(function($alumni) use ($user,$colleges) {
            $flag = false;
            $alumnicolleges = json_decode(User::where('id','=',$alumni->id)->first()->college, true);
            foreach($colleges as $ucollege) {
                foreach($alumnicolleges as $acollege){
                    if($acollege == $ucollege) {
                        $flag = true;
                        break;
                    }
                }
                if($flag) {
                    $alumni->college = College::where('id','=',$alumnicolleges['primary'])->first();
                    return $alumni;
                }
            }
            return null;
        });
        $users = $users->filter()->flatten(1);

        return response()->json($users);
    }

    public function getFriendRequests(Request $request)
    {
        $user = $request->get('user');
        $coords = UserCoords::where('uid', $user->id)->first();
        $query = User::join('friend_requests', 'friend_requests.uid', '=', 'users.id')->where('friend_requests.fid', '=', $user->id)
            ->orderBy('friend_requests.created_at', 'desc')->select('users.*')
            ->where('id', '<>', $user->id)->whereNotNull(['verified_at', 'activated_at'])
            ->with('graduated')->withCount(['friends as shares' => function ($query) {
                $query->where('shared', '=', 1);
            }]);
        $query = $this->buildNormalDistanceQuery($query, $coords);

        $users = $query->skip($request->query('count'))->take(20)->get()->map(function ($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });

        return response()->json($users);
    }

    public function getSuggests(Request $request)
    {
        $user = $request->get('user');
        $coords = UserCoords::where('uid', $user->id)->first();
        $colleges = json_decode($user->college, true);
        $query = User::where('id', '<>', $user->id)->whereNotNull(['verified_at', 'activated_at'])
            ->whereNotIn('id', function ($query) use ($user) {
                $query->selectRaw('uid')->from('friend_requests')->where('fid', $user->id);
            })
            ->whereNotIn('id', function ($query) use ($user) {
                $query->selectRaw('fid')->from('friend_requests')->where('uid', $user->id);
            })
            ->whereNotIn('id', function ($query) use ($user) {
                $query->selectRaw('uid')->from('friends')->where('fid', $user->id);
            })
            ->select('users.*')
            ->with('graduated')->withCount(['friends as shares' => function ($query) {
                $query->where('shared', '=', 1);
            }]);
        $query = $this->buildNormalDistanceQuery($query, $coords);

        $users = $query->orderBy('created_at', 'desc')->skip($request->query('count'))->take(20)->get()->map(function ($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });

        $users = $users->map(function($alumni) use ($user,$colleges) {
            $flag = false;
            $alumnicolleges = json_decode(User::where('id','=',$alumni->id)->first()->college, true);
            foreach($colleges as $ucollege) {
                foreach($alumnicolleges as $acollege){
                    if($acollege == $ucollege) {
                        $flag = true;
                        break;
                    }
                }
                if($flag) {
                    $alumni->college = College::where('id','=',$alumnicolleges['primary'])->first();
                    return $alumni;
                }
            }
            return null;
        });
        $users = $users->filter()->flatten(1);

        return response()->json($users);
    }

    public function getVisits(Request $request)
    {
        $user = $request->get('user');
        $coords = UserCoords::where('uid', $user->id)->first();
        $colleges = json_decode($user->college, true);
        $query = User::where('id', '<>', $user->id)->whereNotNull(['verified_at', 'activated_at'])
            ->whereIn('id', function ($query) use ($user) {
                $query->selectRaw('uid')->from('visits')->where('vid', $user->id);
            })
            ->with('graduated')->withCount(['friends as shares' => function ($query) {
                $query->where('shared', '=', 1);
            }]);
        $query = $this->buildNormalDistanceQuery($query, $coords);

        $users = $query->orderBy('created_at', 'desc')->skip($request->query('count'))->take(20)->get()->map(function ($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });

        $users = $users->map(function($alumni) use ($user,$colleges) {
            $flag = false;
            $alumnicolleges = json_decode(User::where('id','=',$alumni->id)->first()->college, true);
            foreach($colleges as $ucollege) {
                foreach($alumnicolleges as $acollege){
                    if($acollege == $ucollege) {
                        $flag = true;
                        break;
                    }
                }
                if($flag) {
                    $alumni->college = College::where('id','=',$alumnicolleges['primary'])->first();
                    return $alumni;
                }
            }
            return null;
        });
        $users = $users->filter()->flatten(1);

        return response()->json($users);
    }

    public function getFriends(Request $request)
    {
        $user = $request->get('user');
        $coords = UserCoords::where('uid', $user->id)->first();
        $colleges = json_decode($user->college, true);
        $query = User::where('id', '<>', $user->id)->whereNotNull(['verified_at', 'activated_at'])
            ->join('friends', 'friends.fid', '=', 'users.id')->where('friends.uid', '=', $user->id)->select('users.*', 'friends.shared')
            ->with('graduated')->withCount(['friends as shares' => function ($query) {
                $query->where('shared', '=', 1);
            }]);
        $query = $this->buildNormalDistanceQuery($query, $coords);

        $users = $query->skip($request->query('count'))->take(20)->get()->map(function ($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });

        $users = $users->map(function($alumni) use ($user,$colleges) {
            $flag = false;
            $alumnicolleges = json_decode(User::where('id','=',$alumni->id)->first()->college, true);
            foreach($colleges as $ucollege) {
                foreach($alumnicolleges as $acollege){
                    if($acollege == $ucollege) {
                        $flag = true;
                        break;
                    }
                }
                if($flag) {
                    $alumni->college = College::where('id','=',$alumnicolleges['primary'])->first();
                    return $alumni;
                }
            }
            return null;
        });
        $users = $users->filter()->flatten(1);

        return response()->json($users->reverse()->values());
    }
    public function getBlockedUsers(Request $request) {
        $user = $request->get('user');
        $coords = UserCoords::where('uid', $user->id)->first();
        $colleges = json_decode($user->college, true);
        $query = User::where('id', '<>', $user->id)->whereNotNull(['verified_at', 'activated_at'])
            ->join('blocked_users', 'blocked_users.fid', '=', 'users.id')->where('blocked_users.uid', '=', $user->id)
            ->orderBy('blocked_users.created_at', 'desc')->select('users.*')
            ->with('graduated')->withCount(['friends as shares' => function ($query) {
                $query->where('shared', '=', 1);
            }]);
        $query = $this->buildNormalDistanceQuery($query, $coords);

        $users = $query->skip($request->query('count'))->take(20)->get()->map(function ($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });
        $users = $users->map(function($alumni) use ($user,$colleges) {
            $flag = false;
            $alumnicolleges = json_decode(User::where('id','=',$alumni->id)->first()->college, true);
            foreach($colleges as $ucollege) {
                foreach($alumnicolleges as $acollege){
                    if($acollege == $ucollege) {
                        $flag = true;
                        break;
                    }
                }
                if($flag) {
                    $alumni->college = College::where('id','=',$alumnicolleges['primary'])->first();
                    return $alumni;
                }
            }
            return null;
        });
        $users = $users->filter()->flatten(1);

        return response()->json($users);
    }

    public function blockUser(Request $request, $uid) {
        $user = $request->get('user');
        $this->block($user->id, $uid, isset($request['msg'])?$request['msg']:'',$request['block']);
        // $data = [
        //     'uid' => $user->id,
        //     'fid' => $uid,
        //     'block' => $request['block']
        // ];
        // event(new BlockedUser($data));
        return response()->json(true);
    }

    public function getPendings(Request $request)
    {
        $user = $request->get('user');
        $coords = UserCoords::where('uid', $user->id)->first();
        $colleges = json_decode($user->college, true);
        $query = User::where('id', '<>', $user->id)->whereNotNull(['verified_at', 'activated_at'])
            ->join('friend_requests', 'friend_requests.fid', '=', 'users.id')->where('friend_requests.uid', '=', $user->id)
            ->orderBy('friend_requests.created_at', 'desc')->select('users.*')
            ->with('graduated')->withCount(['friends as shares' => function ($query) {
                $query->where('shared', '=', 1);
            }]);
        $query = $this->buildNormalDistanceQuery($query, $coords);

        $users = $query->skip($request->query('count'))->take(20)->get()->map(function ($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });

        $users = $users->map(function($alumni) use ($user,$colleges) {
            $flag = false;
            $alumnicolleges = json_decode(User::where('id','=',$alumni->id)->first()->college, true);
            foreach($colleges as $ucollege) {
                foreach($alumnicolleges as $acollege){
                    if($acollege == $ucollege) {
                        $flag = true;
                        break;
                    }
                }
                if($flag) {
                    $alumni->college = College::where('id','=',$alumnicolleges['primary'])->first();
                    return $alumni;
                }
            }
            return null;
        });
        $users = $users->filter()->flatten(1);

        return response()->json($users);
    }

    public function getSimilarUsers(Request $request, $category, $cid)
    {
        $user = $request->get('user');
        $coords = UserCoords::where('uid', $user->id)->first();
        $colleges = json_decode($user->college, true);
        $query = User::where('id', '<>', $user->id)->whereNotNull(['verified_at', 'activated_at'])
            ->with('graduated')->withCount(['friends as shares' => function ($query) {
                $query->where('shared', '=', 1);
            }]);

        if (strtolower($category) === 'degree') {
            $query->whereHas('degrees', function ($query) use ($cid) {
                $query->where('degree', $cid);
            });
        } else {
            $query->whereHas('orgs', function ($query) use ($cid) {
                $query->where('org', $cid);
            });
        }

        $query = $this->buildNormalDistanceQuery($query, $coords);

        $users = $query->orderBy('created_at', 'desc')->skip($request->query('count'))->take(20)->get()->map(function ($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });

        $users = $users->map(function($alumni) use ($user,$colleges) {
            $flag = false;
            $alumnicolleges = json_decode(User::where('id','=',$alumni->id)->first()->college, true);
            foreach($colleges as $ucollege) {
                foreach($alumnicolleges as $acollege){
                    if($acollege == $ucollege) {
                        $flag = true;
                        break;
                    }
                }
                if($flag) {
                    $alumni->college = College::where('id','=',$alumnicolleges['primary'])->first();
                    return $alumni;
                }
            }
            return null;
        });
        $users = $users->filter()->flatten(1);

        return response()->json($users);
    }

    public function searchUsers(Request $request)
    {
        $user = $request->get('user');
        $colleges = json_decode($user->college, true);
        $query = User::where('id', '<>', $user->id)->whereNotNull(['verified_at', 'activated_at']);
        if ($request->has('keyword')) {
            $query->where(DB::raw('concat(first_name, " ", last_name)'), 'like', '%' . $request['keyword'] . '%');
        }
        if ($request->has('degree')) {
            $query->whereHas('degrees', function ($query) use ($request) {
                $query->where('degree', $request['degree']);
            });
        }
        if ($request->has('industry')) {
            $query->whereHas('industries', function ($query) use ($request) {
                $query->where('industry', $request['industry']);
            });
        }
        if ($request->has('org')) {
            $query->whereHas('orgs', function ($query) use ($request) {
                $query->where('org', $request['org']);
            });
        }
        if ($request->has('zipcode')) {
            $query->join('user_homes', 'user_homes.uid', '=', 'users.id')->where('user_homes.zip', '=', $request['zip'])->select('users.*');
        }
        if ($request->has('religion')) {
            $query->join('user_religions', 'user_religions.uid', '=', 'users.id')->where('user_religions.religion', '=', $request['religion'])->select('users.*');
        }
        if ($request->has('relationship')) {
            $query->join('user_relationships', 'user_relationships.uid', '=', 'users.id')->where('user_relationships.relationship', '=', $request['relationship'])->select('users.*');
        }
        if ($request->has('gender')) {
            $query->join('user_gender_ages', 'user_gender_ages.uid', '=', 'users.id')->where('user_gender_ages.gender', '=', $request['gender'])->select('users.*');
        }

        $coords = UserCoords::where('uid', $user->id)->first();
        $query->with('graduated')->withCount(['friends as shares' => function ($query) {
            $query->where('shared', '=', 1);
        }]);

        $query = $this->buildNormalDistanceQuery($query, $coords);

        $users = $query->orderBy('created_at', 'desc')->skip($request->query('count'))->take(20)->get()->map(function ($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });

        $users = $users->map(function($alumni) use ($user,$colleges) {
            $flag = false;
            $alumnicolleges = json_decode(User::where('id','=',$alumni->id)->first()->college, true);
            foreach($colleges as $ucollege) {
                foreach($alumnicolleges as $acollege){
                    if($acollege == $ucollege) {
                        $flag = true;
                        break;
                    }
                }
                if($flag) {
                    $alumni->college = College::where('id','=',$alumnicolleges['primary'])->first();
                    return $alumni;
                }
            }
            return null;
        });
        $users = $users->filter()->flatten(1);

        return response()->json($users);
    }

    public function getMiniAlumniData(Request $request, $uid)
    {
        $user = $request->get('user');
        $graduated = UserDegree::where('uid', $uid)->orderBy('year', 'desc')->first();
        $shares = Friend::where('uid', $uid)->where('shared', '=', 1)->count();
        $distance = $this->getDistance($user->id, $uid);
        $colleges = json_decode($user->college, true);

        $query = User::where([['id', '<>', $uid], ['id', '<>', $user->id]])
            ->whereNotNull(['verified_at', 'activated_at'])
            ->join('friends', 'friends.fid', '=', 'users.id')->where([['friends.uid', '=', $uid], ['friends.uid', '<>', $user->id], ['shared', '=', 1]])
            ->select('users.*', 'friends.shared');

        $friends = $query->get()->map(function ($u) use ($user) {
            $u->match = $this->getMatchPercent($user->id, $u->id);
            return $u;
        });

        $friends = $friends->map(function($alumni) use ($user,$colleges) {
            $flag = false;
            $alumnicolleges = json_decode(User::where('id','=',$alumni->id)->first()->college, true);
            foreach($colleges as $ucollege) {
                foreach($alumnicolleges as $acollege){
                    if($acollege == $ucollege) {
                        $flag = true;
                        break;
                    }
                }
                if($flag) {
                    $alumni->college = College::where('id','=',$alumnicolleges['primary'])->first();
                    return $alumni;
                }
            }
            return null;
        });
        $friends = $friends->filter()->flatten(1);

        return response()->json([
            'graduated' => $graduated,
            'shares' => $shares,
            'distance' => $distance,
            'friends' => $friends
        ]);
    }

    public function getFullAlumniData(Request $request, $uid)
    {
        $user = $request->get('user');
        $colleges = json_decode($user->college, true);

        $v = Visit::firstOrCreate(
            [
                'uid' => $user->id,
                'vid' => $uid,
            ],
            [
                'count' => 1
            ]);

        $alumni = User::where('id', $uid)
            ->with(['graduated', 'degrees', 'orgs'])->withCount(['friends', 'visits'])->first();


        $alumni->match = $this->getMatchPercent($user->id, $uid);

        $query = User::where([['id', '<>', $uid], ['id', '<>', $user->id]])
            ->whereNotNull(['verified_at', 'activated_at'])
            ->join('friends', 'friends.fid', '=', 'users.id')->where([['friends.uid', '=', $uid], ['friends.uid', '<>', $user->id], ['shared', '=', 1]])
            ->select('users.*', 'friends.shared');

        $friends = $query->get()->map(function ($u) use ($user) {
            $u->match = $this->getMatchPercent($user->id, $u->id);
            $u->shares = $u->shared;
            unset($u->shared);
            return $u;
        });

        $friends = $friends->map(function($alumni) use ($user,$colleges) {
            $flag = false;
            $alumnicolleges = json_decode(User::where('id','=',$alumni->id)->first()->college, true);
            foreach($colleges as $ucollege) {
                foreach($alumnicolleges as $acollege){
                    if($acollege == $ucollege) {
                        $flag = true;
                        break;
                    }
                }
                if($flag) {
                    $alumni->college = College::where('id','=',$alumnicolleges['primary'])->first();
                    return $alumni;
                }
            }
            return null;
        });
        $friends = $friends->filter()->flatten(1);

        $ps = [
            'degrees' => UserDegree::where('uid', $uid)->with('degree')->with('ibc')->get(),
            'athlete' => UserAthlete::where([['uid', '=', $uid], ['privacy', '=', 1]])->with('athlete')->first(),
            'orgs' => UserOrg::where('uid', $uid)->with('org')->get()
        ];

        $cl = [
            'gender_age' => DB::table('user_gender_ages')->where('uid', $uid)->first(),
            'ethnicity' => DB::table('user_ethnicities')->where([['uid', '=', $uid], ['privacy', '=', 1]])->first(),
            'speak_languages' => DB::table('user_speak_languages')->where('uid', $uid)->get(),
            'learn_languages' => DB::table('user_learn_languages')->where('uid', $uid)->get(),
            'religion' => DB::table('user_religions')->where('uid', $uid)->first(),
            'relationship' => DB::table('user_relationships')->where('uid', $uid)->first(),
            'work' => DB::table('user_work_careers')->where('uid', $uid)->first(),
            'industry' => UserIndustry::where('uid',$uid)->with('industry')->get(),
            'home' => DB::table('user_homes')->where('uid', $uid)->first(),
            'hometown' => DB::table('user_hometowns')->where('uid', $uid)->first(),
            'hobbies' => UserHobby::where('uid', $uid)->with('hobby')->get(),
            'causes' => DB::table('user_causes')->where('uid', $uid)->get(),
            'school' => DB::table('user_schools')->where('uid', $uid)->first(),
        ];
        $is_friend = Friend::where('uid', $user->id)->where('fid', $uid)->exists();
        if (!$is_friend) {
            $is_pending = DB::table('friend_requests')->where([['uid', '=', $user->id], ['fid', '=', $uid]])->exists();
            $is_request = DB::table('friend_requests')->where([['uid', '=', $uid], ['fid', '=', $user->id]])->exists();
            if ($is_request) {
                $alumni->is_friend = 10;
            } else if ($is_pending) {
                $alumni->is_friend = 20;
            } else {
                $alumni->is_friend = 0;
            }
        } else {
            $alumni->is_friend = 1;
        }

        $posts = Post::where('userId','=',$uid)->with('user')->with('type')->with('category')->withCount('likes')->withCount("comments")->orderBy('created_at', 'desc')->get();
        $posts = $posts->map(function ($post) {
            $post->college = json_decode(User::where('id','=',$post->userId)->first()->college, true)['primary'];
            $post->college = College::where('id','=',$post->college)->first();
            return $post;
    });

        

        return response()->json(compact('alumni', 'friends', 'ps', 'cl','posts'));
    }
}