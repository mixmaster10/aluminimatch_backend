<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\UserWorkCareer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use App\Traits\PushTrait;
use App\Traits\MatchTrait;

class WorkTitleNotification
{
    use PushTrait;
    use MatchTrait;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // dd($event->params['work_title']);
        $user = User::where('id', $event->params['user']->id)->first();
        $users = User::join('user_match_weights','user_match_weights.uid','=','users.id')->whereNotNull(['verified_at', 'activated_at'])->where('id', '<>', $user->id)->get()
        ->map(function($alumni) use ($user) {
            $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
            return $alumni;
        });
        $work_titles = [
            'CEO',
            'Chief Finance Officer',
            'Chief Marketing Officer',
            'Chief Tech Officer',
            'HR Director',
            'President',
            'Vice President',
            'Senior Manager',
            'Director',
            'Manager',
            'Investor',
            'Analyst',
            'Specialist',
            'Consultant',
            'None of these'
        ];
        foreach ($users as $alumni) {

            $alumniTokens = DB::table('user_devices')->where('uid', $alumni->id)->first();
            if (!is_null($alumniTokens)) {
                $tokenArr = explode(',', $alumniTokens->tokens);
                if (count($tokenArr) > 0) {
                    if ($event->params['work_title'] != null && $event->params['work_title'] != 'None of these') {

                        $alumniWorkCareer = UserWorkCareer::where('uid', $alumni->id)->where('work_title', $event->params['work_title'])->first();
                        if ($alumniWorkCareer !== '' && $alumni->match > 20) {

                            $title = $user->first_name . ' ' . $user->last_name . ' just joined.';
                            $msg = $user->first_name . ' ' . $user->last_name .' ('.$alumni->match.'%)'.' is a (' . $work_titles[$event->params['work_title']] . ') like you! Shake hands! 💼 ';
                            $this->sendMultiPush(3, $tokenArr, $title, $msg, $user,"alumnimatch://com.alumni.app/#/home/user/".$user->id);
                        }
                    }
                }
            }
        }
    }
}
