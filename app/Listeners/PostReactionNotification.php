<?php

namespace App\Listeners;

use App\Models\User;
use App\Traits\MatchTrait;
use App\Traits\PushTrait;
use App\Models\PostLikes;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostReactionNotification
{
    use PushTrait;
    use MatchTrait;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event) {
        $postUser = DB::table('posts')->where('id', $event->params['postId'])->first();

        $userTokens = DB::table('user_devices')->where('uid', $postUser->userId)->first();
        if (!is_null($userTokens)) {
            $tokenArr = explode(',', $userTokens->tokens);
            $user = User::where('id', $event->params['uid'])->first();
            if (count($tokenArr) > 0) {
                if($event->params['reactionType'] === "like") {
                    $timestamp = PostLikes::where('likedBy','=',$user->id)
                    ->where('postId','=',$event->params['postId'])
                    ->orderBy('created_at','desc')
                    ->skip(1)
                    ->first();
                    if($timestamp && Carbon::parse($timestamp)->add('minute', 5)->gt(Carbon::now()))
                        return;
                    $title = 'Post Liked';
                    $text = "has liked your post! Take a look at their profile.";
                }
                if($event->params['reactionType'] === "comment") {
                    $title = 'Post Liked';
                    $text = "commented on your post! Read the comment here.";
                }
                if($event->params['reactionType'] === "report") {
                    $appUrl = "alumnimatch://com.alumni.app/#/home/bulletinboard/details/".$event->params['postId'];
                    $title = "A New Post is in The Report Queue";
                    $text = "has reported a post.";
                    $userTokens = DB::table('user_devices')->where('uid', '1')->first();
                    $tokenArr = explode(',', $userTokens->tokens);
                    $user->appUrl = $appUrl;
                    $msg = $user->first_name.' '.$user->last_name.' '.$text;
                    $this->sendMultiPush(6, $tokenArr, $title, $msg, $user);
                    return;
                }
                $matchPercentage = $this->getMatchPercent($user->id, $event->params['loggedUserId']);
                $appUrl = "alumnimatch://com.alumni.app/#/home/bulletinboard/details/".$event->params['postId'];
                $user->appUrl = $appUrl;
                $msg = $user->first_name.' ('.$matchPercentage.'%) '.$text;
                $this->sendMultiPush(6, $tokenArr, $title, $msg, $user);
            }
        }
    }
}
