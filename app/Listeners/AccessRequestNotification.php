<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\PushTrait;
use Illuminate\Support\Facades\DB;

class AccessRequestNotification
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
        $userToken = DB::table('user_devices')->where('uid', $event->params['uid'])->first();
        $user = User::where('id', $event->params['uid'])->first();
        $requested_user = User::where('id', $event->params['requested_user'])->first();
       if (!is_null($userToken)) {
            $tokenArr = explode(',', $userToken->tokens);
            if (count($tokenArr) > 0) {
                $title = 'Access Request';
                $msg = $requested_user->first_name.' '.$requested_user->last_name.' wants to see your'. $event->params['access'];
                $appUrl = "alumnimatch://com.alumni.app/#/company";
                $user->appUrl = $appUrl;
                $this->sendMultiPush(10, $tokenArr, $title, $msg, $user);
            }
        }
           
    }
}
