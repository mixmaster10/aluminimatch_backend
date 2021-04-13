<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\UserCoords;
use App\Traits\DistanceTrait;
use App\Traits\MatchTrait;
use App\Traits\PushTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class FriendApproveNotification
{
    use PushTrait;
    use MatchTrait;
    use DistanceTrait;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $userTokens = DB::table('user_devices')->where('uid', $event->params['fid'])->first();
        if (!is_null($userTokens)) {
            $tokenArr = explode(',', $userTokens->tokens);
            if (count($tokenArr) > 0) {
                $coords = UserCoords::where('uid', $event->params['fid'])->first();
                $query = User::where('id', $event->params['uid'])
                    ->with('graduated')->withCount(['friends as shares' => function($query) {$query->where('shared', '=', 1);}]);
                $query = $this->buildNormalDistanceQuery($query, $coords);
                $alumni = $query->first();
                $alumni->match = $this->getMatchPercent($event->params['fid'], $event->params['uid']);

                $title = 'Friend request was approved';
                $msg = 'Your friend request approved by '.$alumni->first_name.' '.$alumni->last_name.' ('.$alumni->match.'%)';
                $appUrl = "alumnimatch://com.alumni.app/#/home/user/".$alumni->id;
                $alumni->appUrl = $appUrl;
                $this->sendMultiPush(3, $tokenArr, $title, $msg, $alumni);
            }
        }
    }
}
