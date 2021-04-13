<?php

namespace App\Listeners;

use App\Events\FriendRequested;
use App\Models\User;
use App\Models\UserAthlete;
use App\Models\UserOrg;
use App\Models\Visit;
use App\Traits\FriendTrait;
use App\Traits\MatchTrait;
use App\Traits\PushTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\UserWorkCareer;

use config\constants;

class NewJoinNotification
{
    use FriendTrait;
    use MatchTrait;
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
        DB::table('user_logins')->updateOrInsert(
            [
                'uid' => $event->params['uid']
            ],
            [
                'count' => 1
            ]
        );
        $user = User::where('id', $event->params['uid'])->first();
        $colleges = json_decode($user->college,true);
        $userOrgs = UserOrg::where('uid', $user->id)->get();
        $userWorkCareers = UserWorkCareer::where('uid',$user->id)->first();
        $userAthletes = UserAthlete::where('uid', $user->id)->with('athlete')->first();
        // $admin = User::where('id', 1)->first();
        // if ($user->college === $admin->college) {
        //     Visit::firstOrCreate(
        //         [
        //             'uid' => 1,
        //             'vid' => $event->params['uid']
        //         ],
        //         [
        //             'count' => 1
        //         ]
        //     );
        //     //send 1 message from Jonathon
        //     $body = 'Welcome to Our Great Community! It is my great pleasure to welcome you to the AlumniMatch community. Here you will find a fun, safe, and trusted digital connection between you and all of your college alumni. My advice to you when getting started is to simply click on each of the sections in the Navigation, which can be found in the bottom left by clicking the Menu looking button. You can view my profile to learn more about me and PLEASE be sure to update your location settings to ON and let other alumni know where you are in a controlled, secure way. Build incredible relationships and live a more fulfilling and meaningful life because of people from your alma mater. Sincerely, Jon Lunardi, Creator of AlumniMatch';

        //     $this->invite(1, $event->params['uid'], $body);
        //     $data = [
        //         'uid' => 1,
        //         'fid' => $event->params['uid'],
        //         'msg' => $body
        //     ];
        //     event(new FriendRequested($data));
        // }
        $query = User::query();
        $users = $query->where('id', '<>', $user->id)->whereNotNull(['verified_at', 'activated_at']);
        $users = $query->get();

        $users = $users->map(function($alumni) use ($user,$colleges) {
                $alumni->match = $this->getMatchPercent($user->id, $alumni->id);
                $alumcol = json_decode($alumni->college,true);
                foreach($alumcol as $college){
                    if($college == $colleges['primary'])
                        return $alumni;
                }
                return null;
            });
        $users = $users->filter()->flatten(1);
        foreach ($users as $alumni){
            $matchOrgName = '';
            $alumniOrgs = UserOrg::where('uid', $alumni->id)->get();
            $ORGANIZATIONS = constants::Orgs();
            foreach($userOrgs as $org) {
                foreach($alumniOrgs as $alumorg) {
                    if ($org['org'] && $org['org'] === $alumorg['org']) {
                        $matchOrgName = $ORGANIZATIONS[$org['org']-1];
                        break;
                    }
                }
                if ($matchOrgName !== '') {
                    break;
                }
            }

            $alumniAthlete = UserAthlete::where('uid', $alumni->id)->with('athlete')->first();
           
            $matchAthleteName = '';
            // dd($userAthletes);
            if (!is_null($userAthletes['athlete'])) {
                if ($userAthletes['athlete'] === $alumniAthlete['athlete']) {
                    $matchAthleteName = DB::table('athletes')->where('id','=',$userAthletes['athlete'])->first()->name;
                }
            }
            $alumniTokens = DB::table('user_devices')->where('uid', $alumni->id)->first();
            if (!is_null($alumniTokens)) {
                $tokenArr = explode(',', $alumniTokens->tokens);
                if (count($tokenArr) > 0) {
                    if($userWorkCareers && $userWorkCareers->work_title != 14) {
                        $alumniWorkCareer = UserWorkCareer::where('uid',$alumni->id)->where('work_title',$userWorkCareers->work_title)->first();
                    if (!is_null($alumniWorkCareer)) {
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
                        $appUrl = "alumnimatch://com.alumni.app/#/home/user/".$user->id;
                        $user->appUrl = $appUrl;
                        $title = $user->first_name.' ('.$alumni->match.'%) has the same job title as you!';
                        $msg = $user->first_name.' '.$user->last_name.' is a '.$work_titles[$userWorkCareers->work_title].' like you! You two should compare notes (or just vent)! 🤝';
                        $this->sendMultiPush(3, $tokenArr, $title, $msg, $user,$appUrl);
                    }
                }
                    if ($matchOrgName !== '') {
                        $appUrl = "alumnimatch://com.alumni.app/#/home/user/".$user->id;
                        $user->appUrl = $appUrl;
                        $title = $user->first_name.' ('.$alumni->match.'%) was in the same student organization as you!';
                        $msg = $user->first_name.' '.$user->last_name.' was in the '.$matchOrgName.' org like you! Be the first to say hi!';
                        $this->sendMultiPush(3, $tokenArr, $title, $msg, $user);
                    }
                    if ($matchAthleteName !== '') {
                        $appUrl = "alumnimatch://com.alumni.app/#/home/user/".$user->id;
                        $user->appUrl = $appUrl;
                        $title = $user->first_name.' ('.$alumni->match.'%) was on the same team as you!';
                        $msg = $user->first_name.' '.$user->last_name.' was a '.$matchAthleteName.' player too! Score some points with them, be the first to say hi!';
                        $this->sendMultiPush(3, $tokenArr, $title, $msg, $user);
                    }
                    if ($alumni->match > 29 && $alumni->match < 50) { // "Fair Match" 
                        $appUrl = "alumnimatch://com.alumni.app/#/home/user/".$user->id;
                        $user->appUrl = $appUrl;
                        $title = $user->first_name.' is a '.$alumni->match.'% Match With You!';
                        $msg =  $user->first_name.' '.$user->last_name.' just joined AlumniMatch! you share some interests, so you should say hi!';
                        $this->sendMultiPush(3, $tokenArr, $title, $msg, $user);
                    }
                    if ($alumni->match >= 50 && $alumni->match < 94) { // "Good Match" 
                        $appUrl = "alumnimatch://com.alumni.app/#/home/user/".$user->id;
                        $user->appUrl = $appUrl;
                        $title = $user->first_name.' is a '.$alumni->match.'% Match With You!';
                        $msg =  $user->first_name.' '.$user->last_name.' just joined AlumniMatch! The two of you are really similar, so you should say hi!';
                        $this->sendMultiPush(3, $tokenArr, $title, $msg, $user);
                    }
                    if ($alumni->match > 94) { // "Perfect Match" 
                        $appUrl = "alumnimatch://com.alumni.app/#/home/user/".$user->id;
                        $title = 'Oh Wow, A Perfect Match!';
                        $user->appUrl = $appUrl;
                        $msg =  $user->first_name.' '.$user->last_name.' is a '.$alumni->match.'% Match With You! They just joined, so now\'s your chance to say hi!' ;
                        $this->sendMultiPush(3, $tokenArr, $title, $msg, $user);
                    }
                }
            }
        }
    }
}
