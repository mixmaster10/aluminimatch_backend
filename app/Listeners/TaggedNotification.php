<?php

namespace App\Events;

use App\Traits\MatchTrait;
use App\Traits\PushTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaggedNotification
{
    use PushTrait;
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
        $user = User::where('id', $event->params['uid'])->first();
        $tagged = $event->params['tagged'];
        foreach($tagged as $tag){
            $tag = User::where('id',$tag)->first();
            $alumniTokens = DB::table('user_devices')->where('uid', $tag->id)->first();
            if (!is_null($alumniTokens)) {
                $tokenArr = explode(',', $alumniTokens->tokens);
                if (count($tokenArr) > 0) {
                    $title = $user->first_name.' Tagged You!';
                    $msg = $user->first_name.' '.$user->last_name.' tagged you in their recent post.';
                    $appUrl = "alumnimatch://com.alumni.app/#/home/bulletinboard/details/".$event->params['postId'];
                    $this->sendMultiPush(3, $tokenArr, $title, $msg, $user,$appUrl);
                }
            }
        }
    }
}
