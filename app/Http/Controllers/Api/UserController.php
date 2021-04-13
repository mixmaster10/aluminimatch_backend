<?php

namespace App\Http\Controllers\Api;

use App\Events\AccessRequest as EventsAccessRequest;
use App\Events\LocationUpdated;
use App\Events\NewJoined;
use App\Models\College;
use App\Models\Friend;
use App\Models\Industry;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserAthlete;
use App\Models\UserCoords;
use App\Models\UserDegree;
use App\Models\UserHobby;
use App\Models\UserActivity;
use App\Models\UserIndustry;
use App\Models\UserMatchWeight;
use App\Models\UserOrg;
use App\Models\UserSocial;
use App\Models\AccessRequest;
use App\Traits\DistanceTrait;
use App\Events\WorkTitle;
use App\Traits\MatchTrait;
use Carbon\Carbon;
use Faker\Provider\File;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use Mockery\Exception;
use App\Traits\AdsResponseTrait;
use App\Traits\ApiResponser;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Notifications\SendWelcomeEmail;

class UserController extends Controller
{

    use DistanceTrait;
    use MatchTrait;
    use AdsResponseTrait;
    use ApiResponser;

    public function index(Request $request)
    {
        $user = $request->get('user');
        // dd($user);
        return $this->dataOnlyResponse($user->id);
    }

    public function getMatchWeights(Request $request)
    {
        $user = $request->get('user');
        $weights = UserMatchWeight::firstOrCreate(
            [
                "uid" => $user->id
            ],
            [
                "ps" => 25,
                "cl" => 75
            ]
        );
        return response()->json($weights);
    }

    public function saveMatchWeights(Request $request)
    {
        $user = $request->get('user');
        UserMatchWeight::updateOrCreate(
            [
                "uid" => $user->id
            ],
            [
                "ps" => $request->get('ps'),
                "cl" => $request->get('cl')
            ]
        );
        return response()->json(true);
    }

    public function getDegrees(Request $request)
    {
        $user = $request->get('user');
        $degrees = UserDegree::where('uid', $user->id)->with('degree')->with('ibc')->get();
        return response()->json($degrees);
    }

    public function saveDegrees(Request $request)
    {
        $user = $request->get('user');
        UserDegree::where('uid', $user->id)->delete();
        foreach ($request->degrees as $degree) {
            UserDegree::create([
                'uid' => $user->id,
                'type' => $degree['type'],
                'degree' => $degree['degree']['id'],
                'year' => $degree['year'],
                'ibc' => isset($degree['ibc']) ? (isset($degree['ibc']['id']) ? $degree['ibc']['id'] : null) : null
            ]);
        }
        return response()->json(true);
    }

    public function getOrgs(Request $request)
    {
        $user = $request->get('user');
        $org_ids = UserOrg::where('uid', $user->id)->pluck('org')->toArray();
        $orgs = Organization::whereIn('id', $org_ids)->get();
        return response()->json($orgs);
    }

    public function saveOrgs(Request $request)
    {
        $user = $request->get('user');
        UserOrg::where('uid', $user->id)->delete();
        if(!$request->orgs){
            UserOrg::create([
                'uid' => $user->id,
                'org' => null
            ]);
            return response()->json(true);
        }
        foreach ($request->orgs as $org) {
            UserOrg::create([
                'uid' => $user->id,
                'org' => $org['id']
            ]);
        }
        return response()->json(true);
    }

    public function getAthlete(Request $request)
    {
        $user = $request->get('user');
        $athlete = UserAthlete::where('uid', $user->id)->with('athlete')->first();
        return response()->json($athlete);
    }

    public function saveAthlete(Request $request)
    {
        $user = $request->get('user');
        UserAthlete::updateOrCreate([
            'uid' => $user->id
        ], [
            'privacy' => $request['privacy'],
            'member' => $request['member'],
            'athlete' => isset($request['athlete']) ? $request['athlete']['id'] : null,
            'position' => isset($request['position']) ? $request['position'] : null,
        ]);
        return response()->json(true);
    }

    //    GAE : Gender, Age, Ethnicity
    public function getGenderAgeEthnicity(Request $request)
    {
        $user = $request->get('user');
        $gender_age = DB::table('user_gender_ages')->where('uid', $user->id)->first();
        $ethnicity = DB::table('user_ethnicities')->where('uid', $user->id)->first();
        return response()->json(compact('gender_age', 'ethnicity'));
    }

    public function saveGenderAgeEthnicity(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_gender_ages')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'gender' => isset($request['gender']) ? $request['gender'] : null,
                'age' => isset($request['age']) ? $request['age'] : null,
            ]
        );
        DB::table('user_ethnicities')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'ethnicity' => isset($request['ethnicity']) && isset($request['ethnicity']['ethnicity']) ? $request['ethnicity']['ethnicity'] : null,
                'privacy' => isset($request['ethnicity']) && isset($request['ethnicity']['privacy']) ? $request['ethnicity']['privacy'] : false
            ]
        );
        return response()->json(true);
    }

    public function accessRequest(Request $request)
    {
        $user = $request->get('user');
        $newRequest = $request->all();
        $newRequest['uid'] = $user->id;
        if (request()->get('approve') == 'false') {
            $data = AccessRequest::create($newRequest);
            event(new EventsAccessRequest($newRequest));
            return response()->json(compact('data'), 200);
        } else {
            $data = AccessRequest::where(['uid' => $request->id, 'requested_user' => $request->requested_user, 'access' => $request->access])->update(['approve' => 'true']);
            $msg = "Request Approved";
            return response()->json(compact('data', 'msg'), 200);
        }
    }
    public function allAccessRequests(Request $request)
    {
        $user = $request->get('user');
        $data = AccessRequest::where('uid', $user->id)->get();
        return response()->json(compact('data','user'), 200);
    }

    public function getSpeakLanguages(Request $request)
    {
        $user = $request->get('user');
        $languages = DB::table('user_speak_languages')->where('uid', $user->id)->pluck('language')->toArray();
        return response()->json($languages);
    }

    public function saveSpeakLanguages(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_speak_languages')->where('uid', $user->id)->delete();
        foreach ($request->all() as $language) {
            DB::table('user_speak_languages')->insert([
                'uid' => $user->id,
                'language' => $language
            ]);
        }
        return response()->json(true);
    }

    public function getLearnLanguages(Request $request)
    {
        $user = $request->get('user');
        $languages = DB::table('user_learn_languages')->where('uid', $user->id)->select('language', 'fluent')->get();
        $ranges = DB::table('user_learn_language_scales')->where('uid', $user->id)->select('fluent', 'level', 'tutor', 'teach')->first();

        return response()->json(compact('languages', 'ranges'));
    }

    public function saveLearnLanguages(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_learn_languages')->where('uid', $user->id)->delete();
        foreach ($request['languages'] as $language) {
            DB::table('user_learn_languages')->insert([
                'uid' => $user->id,
                'language' => $language['language'],
                'fluent' => isset($language['fluent']) ? $language['fluent'] : null
            ]);
        }
        DB::table('user_learn_language_scales')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'fluent' => isset($request['ranges']['fluent']) ? $request['ranges']['fluent'] : null,
                'level' => isset($request['ranges']['level']) ? $request['ranges']['level'] : null,
                'tutor' => isset($request['ranges']['tutor']) ? $request['ranges']['tutor'] : null,
                'teach' => isset($request['ranges']['teach']) ? $request['ranges']['teach'] : null,
            ]
        );
        return response()->json(true);
    }

    public function getReligion(Request $request)
    {
        $user = $request->get('user');
        $religion = DB::table('user_religions')->where('uid', $user->id)->first();
        return response()->json($religion);
    }

    public function saveReligion(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_religions')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'religion' => $request['religion'],
                'church' => isset($request['church']) ? $request['church'] : null,
                'year' => isset($request['year']) ? $request['year'] : null,
                'dating_scale' => isset($request['dating_scale']) ? $request['dating_scale'] : null,
                'friendship_scale' => isset($request['friendship_scale']) ? $request['friendship_scale'] : null,
                'work_scale' => isset($request['work_scale']) ? $request['work_scale'] : null,
                'spiritual_scale' => isset($request['spiritual_scale']) ? $request['spiritual_scale'] : null,
                'general_scale' => isset($request['general_scale']) ? $request['general_scale'] : null,
            ]
        );
        return response()->json(true);
    }

    public function getRelationship(Request $request)
    {
        $user = $request->get('user');
        $relationship = DB::table('user_relationships')->where('uid', $user->id)->first();
        $relationship_married = DB::table('user_relationship_married')->where('uid', $user->id)->first();
        $relationship_single = DB::table('user_relationship_single')->where('uid', $user->id)->first();
        if($relationship_single) {
            if($relationship_single->ethnicity)
                $relationship_single->ethnicity = json_decode($relationship_single->ethnicity);
            if($relationship_single->music)
                $relationship_single->music = json_decode($relationship_single->music);
            if($relationship_single->body_type)
                $relationship_single->body_type = json_decode($relationship_single->body_type);
            if($relationship_single->match_age)
                $relationship_single->match_age = json_decode($relationship_single->match_age);
            if (!is_null($relationship_single)) {
                $relationship_single->foods = DB::table('user_relationship_foods')->where('uid', $user->id)->pluck('food')->toArray();
            }
        }
        $relationship_widowed = DB::table('user_relationship_widowed')->where('uid', $user->id)->first();
        $relationship_other = DB::table('user_relationship_other')->where('uid', $user->id)->first();
        $kids = DB::table('user_relationship_kids')->where('uid', $user->id)->get();
        return response()->json([
            'relationship' => is_null($relationship) ? null : $relationship->relationship,
            'married' => $relationship_married,
            'single' => $relationship_single,
            'widowed' => $relationship_widowed,
            'other' => $relationship_other,
            'kids' => $kids
        ]);
    }

    public function saveRelationshipMarried(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_relationships')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'relationship' => 0
            ]
        );
        DB::table('user_relationship_married')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'is_alumni' => $request->has('is_alumni') ? $request['is_alumni'] : false,
                'meet_couple_scale' => $request->has('meet_couple_scale') ? $request['meet_couple_scale'] : null,
                'year' => $request->has('year') ? $request['year'] : null,
                'privacy_married_year' => $request->has('privacy_married_year') ? $request['privacy_married_year'] : null,
                'have_kids' => $request->has('have_kids') ? $request['have_kids'] : false,
                'meet_kid_scale' => $request->has('meet_kid_scale') ? $request['meet_kid_scale'] : null,
                'meet_married_scale' => $request->has('meet_married_scale') ? $request['meet_married_scale'] : null,
            ]
        );
        $this->saveRelationshipKids($user->id, $request['kids']);
        return response()->json(true);
    }

    public function saveRelationshipDivorced(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_relationships')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'relationship' => 1
            ]
        );
        DB::table('user_relationship_single')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'meet_divorced' => $request->has('meet_divorced') ? $request['meet_divorced'] : false,
                'single_scale' => $request->has('single_scale') ? $request['single_scale'] : null,
                'ethnicity' => $request->has('ethnicity') ? json_encode($request['ethnicity']) : null,
                'music' => $request->has('music') ? json_encode($request['music']) : null,
                'drink' => $request->has('drink') ? $request['drink'] : null,
                'privacy_drink' => $request->has('privacy_drink') ? $request['privacy_drink'] : false,
                'smoke' => $request->has('smoke') ? $request['smoke'] : null,
                'privacy_smoke' => $request->has('privacy_smoke') ? $request['privacy_smoke'] : false,
                'sex_scale' => $request->has('sex_scale') ? $request['sex_scale'] : null,
                'have_pets' => $request->has('have_pets') ? $request['have_pets'] : null,
                'pets' => $request->has('pets') ? $request['pets'] : null,
                'pets_scale' => $request->has('pets_scale') ? $request['pets_scale'] : null,
                'like_pets' => $request->has('like_pets') ? $request['like_pets'] : null,
                'match_age' => $request->has('match_age') ? json_encode($request['match_age']) : null,
                'body_type' => $request->has('body_type') ? json_encode($request['body_type']) : null,
                'privacy_body_type' => $request->has('privacy_body_type') ? $request['privacy_body_type'] : false,
                'own_body_type' => $request->has('own_body_type') ? $request['own_body_type'] : null,
                'privacy_own_body_type' => $request->has('privacy_own_body_type') ? $request['privacy_own_body_type'] : false,
                'laugh' => $request->has('laugh') ? $request['laugh'] : null,
                'privacy_laugh' => $request->has('privacy_laugh') ? $request['privacy_laugh'] : false,
                'laugh_scale' => $request->has('laugh_scale') ? $request['laugh_scale'] : null,
                'married_before' => $request->has('married_before') ? $request['married_before'] : null,
                'married_count' => $request->has('married_count') ? $request['married_count'] : null,
                'married_scale' => $request->has('married_scale') ? $request['married_scale'] : null,
                'have_kids' => $request->has('have_kids') ? $request['have_kids'] : false,
                'kids_scale' => $request->has('kids_scale') ? $request['kids_scale'] : null
            ]
        );
        $this->saveRelationshipKids($user->id, $request['kids']);
        $this->saveRelationshipFoods($user->id, $request['foods']);
        return response()->json(true);
    }

    public function saveRelationshipEngaged(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_relationships')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'relationship' => 2
            ]
        );
        DB::table('user_relationship_married')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'is_alumni' => $request->has('is_alumni') ? $request['is_alumni'] : false,
                'meet_couple_scale' => $request->has('meet_couple_scale') ? $request['meet_couple_scale'] : null,
                'year' => $request->has('year') ? $request['year'] : null,
                'privacy_married_year' => $request->has('privacy_married_year') ? $request['privacy_married_year'] : null,
                'have_kids' => $request->has('have_kids') ? $request['have_kids'] : false,
                'meet_kid_scale' => $request->has('meet_kid_scale') ? $request['meet_kid_scale'] : null,
                'meet_married_scale' => $request->has('meet_married_scale') ? $request['meet_married_scale'] : null,
                'plan_marry_date' => $request->has('plan_marry_date') ? $request['plan_marry_date'] : null,
                'finance' => $request->has('finance') ? $request['finance'] : null,
            ]
        );
        $this->saveRelationshipKids($user->id, $request['kids']);
        return response()->json(true);
    }

    public function saveRelationshipWidowed(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_relationships')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'relationship' => 3
            ]
        );
        $this->saveRelationshipKids($user->id, $request['kids']);
        return response()->json(true);
    }

    public function saveRelationshipSingle(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_relationships')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'relationship' => 4
            ]
        );
        DB::table('user_relationship_single')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'single_scale' => $request->has('single_scale') ? $request['single_scale'] : null,
                'ethnicity' => $request->has('ethnicity') ? json_encode($request['ethnicity']) : null,
                'music' => $request->has('music') ? json_encode($request['music']) : null,
                'drink' => $request->has('drink') ? $request['drink'] : null,
                'privacy_drink' => $request->has('privacy_drink') ? $request['privacy_drink'] : false,
                'smoke' => $request->has('smoke') ? $request['smoke'] : null,
                'privacy_smoke' => $request->has('privacy_smoke') ? $request['privacy_smoke'] : false,
                'sex_scale' => $request->has('sex_scale') ? $request['sex_scale'] : null,
                'have_pets' => $request->has('have_pets') ? $request['have_pets'] : null,
                'pets' => $request->has('pets') ? $request['pets'] : null,
                'pets_scale' => $request->has('pets_scale') ? $request['pets_scale'] : null,
                'like_pets' => $request->has('like_pets') ? $request['like_pets'] : null,
                'match_age' => $request->has('match_age') ? json_encode($request['match_age']) : null,
                'body_type' => $request->has('body_type') ? json_encode($request['body_type']) : null,
                'privacy_body_type' => $request->has('privacy_body_type') ? $request['privacy_body_type'] : false,
                'own_body_type' => $request->has('own_body_type') ? $request['own_body_type'] : null,
                'privacy_own_body_type' => $request->has('privacy_own_body_type') ? $request['privacy_own_body_type'] : false,
                'laugh' => $request->has('laugh') ? $request['laugh'] : null,
                'privacy_laugh' => $request->has('privacy_laugh') ? $request['privacy_laugh'] : false,
                'laugh_scale' => $request->has('laugh_scale') ? $request['laugh_scale'] : null,
                'married_before' => $request->has('married_before') ? $request['married_before'] : null,
                'married_count' => $request->has('married_count') ? $request['married_count'] : null,
                'married_scale' => $request->has('married_scale') ? $request['married_scale'] : null,
                'have_kids' => $request->has('have_kids') ? $request['have_kids'] : false,
                'kids_scale' => $request->has('kids_scale') ? $request['kids_scale'] : null
            ]
        );
        $this->saveRelationshipKids($user->id, $request['kids']);
        $this->saveRelationshipFoods($user->id, $request['foods']);
        return response()->json(true);
    }


    public function saveRelationshipOther(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_relationships')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'relationship' => 5
            ]
        );
        DB::table('user_relationship_other')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'married_year' => $request->has('married_year') ? $request['married_year'] : null,
                'have_kids' => $request->has('have_kids') ? $request['have_kids'] : false
            ]
        );
        $this->saveRelationshipKids($user->id, $request['kids']);
        return response()->json(true);
    }

    private function saveRelationshipKids($uid, $kids)
    {
        DB::table('user_relationship_kids')->where('uid', $uid)->delete();
        foreach ($kids as $kid) {
            if (isset($kid['gender']) && isset($kid['age'])) {
                DB::table('user_relationship_kids')->insert([
                    'uid' => $uid,
                    'gender' => $kid['gender'],
                    'age' => $kid['age']
                ]);
            }
        }
    }

    private function saveRelationshipFoods($uid, $foods)
    {
        DB::table('user_relationship_foods')->where('uid', $uid)->delete();
        if (isset($foods)) {
            foreach ($foods as $food) {
                if (isset($food)) {
                    DB::table('user_relationship_foods')->insert([
                        'uid' => $uid,
                        'food' => $food
                    ]);
                }
            }
        }
    }

    public function invitePartner(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_invited_partners')->updateOrInsert([
            'uid' => $user->id
        ], [
            'email' => $request['email'],
            'phone' => $request['phone']
        ]);
        //        TODO: Send email invitation to partner
        return response()->json(true);
    }

    public function getWorkCareer(Request $request)
    {
        $user = $request->get('user');
        $work_career = DB::table('user_work_careers')->where('uid', $user->id)->first();
        if (!is_null($work_career)) {
            $work_career->buying_stuff = json_decode($work_career->buying_stuff);
            $work_career->customer = json_decode($work_career->customer);
            $work_career->business_cities = DB::table('user_business_cities')->where('uid', $user->id)->get();
            $work_career->travel_cities = DB::table('user_travel_cities')->where('uid', $user->id)->get();
            $industry_ids = UserIndustry::where('uid', $user->id)->pluck('industry')->toArray();
            $work_career->industries = Industry::whereIn('id', $industry_ids)->get();
        }
        return response()->json($work_career);
    }

    public function saveWorkCareer(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_work_careers')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'work_for' => $request->has('work_for') ? $request['work_for'] : null,
                'privacy_business_city' => $request->has('privacy_business_city') ? $request['privacy_business_city'] : false,
                'privacy_travel_city' => $request->has('privacy_travel_city') ? $request['privacy_travel_city'] : false,
                'employment_status' => $request->has('employment_status') ? $request['employment_status'] : null,
                'work_title' => $request->has('work_title') ? $request['work_title'] : null,
                'work_title_scale' => $request->has('work_title_scale') ? $request['work_title_scale'] : null,
                'hire_full' => $request->has('hire_full') ? $request['hire_full'] : null,
                'hire_full_count' => $request->has('hire_full') && $request['hire_full'] && $request->has('hire_full_count') ? $request['hire_full_count'] : null,
                'hire_full_looking' => $request->has('hire_full') && $request['hire_full'] && $request->has('hire_full_looking') ? $request['hire_full_looking'] : null,
                'hire_full_for' => $request->has('hire_full') && $request['hire_full'] && $request->has('hire_full_for') ? $request['hire_full_for'] : null,
                'privacy_hire_full' => $request->has('privacy_hire_full') ? $request['privacy_hire_full'] : false,
                'hire_gig' => $request->has('hire_gig') ? $request['hire_gig'] : null,
                'hire_gig_count' => $request->has('hire_gig') && $request['hire_gig'] && $request->has('hire_gig_count') ? $request['hire_gig_count'] : null,
                'privacy_hire_gig' => $request->has('privacy_hire_gig') ? $request['privacy_hire_gig'] : false,
                'hire_intern' => $request->has('hire_intern') ? $request['hire_intern'] : null,
                'hire_intern_count' => $request->has('hire_intern') && $request['hire_intern'] && $request->has('hire_intern_count') ? $request['hire_intern_count'] : null,
                'hire_intern_looking' => $request->has('hire_intern') && $request['hire_intern'] && $request->has('hire_intern_looking') ? $request['hire_intern_looking'] : null,
                'hire_intern_for' => $request->has('hire_intern') && $request['hire_intern'] && $request->has('hire_intern_for') ? $request['hire_intern_for'] : null,
                'privacy_hire_intern' => $request->has('privacy_hire_intern') ? $request['privacy_hire_intern'] : false,
                'own_business' => $request->has('own_business') ? $request['own_business'] : null,
                'seeking_investment' => $request->has('own_business') && $request['own_business'] === 0 && $request->has('seeking_investment') ? $request['seeking_investment'] : null,
                'buying_stuff' => $request->has('own_business') && $request['own_business'] === 0 && $request->has('buying_stuff') ? json_encode($request['buying_stuff']) : null,
                'customer' => $request->has('own_business') && $request['own_business'] === 0 && $request->has('customer') ? json_encode($request['customer']) : null,
                'investor' => $request->has('investor') ? $request['investor'] : null,
                'wealth' => $request->has('investor') && $request['investor'] && $request->has('wealth') ? $request['wealth'] : null,
                'wealth_scale' => $request->has('investor') && $request['investor'] && $request->has('wealth_scale') ? $request['wealth_scale'] : null,
                'review_plan' => $request->has('investor') && $request['investor'] && $request->has('review_plan') ? $request['review_plan'] : null,
                'privacy_investor' => $request->has('privacy_investor') ? $request['privacy_investor'] : false
            ]
        );
        DB::table('user_business_cities')->where('uid', $user->id)->delete();
        if ($request->has('business_cities')) {
            foreach ($request['business_cities'] as $city) {
                DB::table('user_business_cities')->insert([
                    'uid' => $user->id,
                    'country' => $city['country'],
                    'state' => $city['state'],
                    'city' => $city['city']
                ]);
            }
        }
        DB::table('user_travel_cities')->where('uid', $user->id)->delete();
        if ($request->has('travel_cities')) {
            foreach ($request['travel_cities'] as $city) {
                DB::table('user_travel_cities')->insert([
                    'uid' => $user->id,
                    'country' => $city['country'],
                    'state' => $city['state'],
                    'city' => $city['city']
                ]);
            }
        }
        UserIndustry::where('uid', $user->id)->delete();
        if ($request->has('industries')) {
            foreach ($request['industries'] as $industry) {
                UserIndustry::create([
                    'uid' => $user->id,
                    'industry' => $industry['id']
                ]);
            }
        }
        $data['user'] = $user;
        $data['work_title'] = $request->has('work_title') ? $request['work_title'] : null;
        //event(new WorkTitle($data));
        return response()->json(true);
    }

    public function getHome(Request $request)
    {
        $user = $request->get('user');
        $home = DB::table('user_homes')->where('uid', $user->id)->first();
        if (is_null($home)) {
            $college = College::where('id', $user->college)->with('country')->with('state')->first();
            $home = [
                'country' => $college->country->name,
                'state' => isset($college->state) ? $college->state->name : ''
            ];
        }
        return response()->json($home);
    }

    public function saveHome(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_homes')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'country' => $request->has('country') ? $request['country'] : null,
                'state' => $request->has('state') ? $request['state'] : null,
                'zip' => $request->has('zip') ? $request['zip'] : null,
                'scale' => $request->has('scale') ? $request['scale'] : null,
                'game_scale' => $request->has('game_scale') ? $request['game_scale'] : null,
                'event_scale' => $request->has('event_scale') ? $request['event_scale'] : null,
                'privacy' => $request->has('privacy') ? $request['privacy'] : null
            ]
        );
        return response()->json(true);
    }

    public function getHometown(Request $request)
    {
        $user = $request->get('user');
        $hometown = DB::table('user_hometowns')->where('uid', $user->id)->first();
        if (is_null($hometown)) {
            $college = College::where('id', $user->college)->with('country')->with('state')->first();
            $hometown = [
                'country' => $college->country->name,
                'state' => isset($college->state) ? $college->state->name : ''
            ];
        }
        return response()->json($hometown);
    }

    public function saveHometown(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_hometowns')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'country' => $request->has('country') ? $request['country'] : null,
                'state' => $request->has('state') ? $request['state'] : null,
                'zip' => $request->has('zip') ? $request['zip'] : null,
                'scale' => $request->has('scale') ? $request['scale'] : null,
                'privacy' => $request->has('privacy') ? $request['privacy'] : null
            ]
        );
        return response()->json(true);
    }

    public function getHobbies(Request $request)
    {
        $user = $request->get('user');
        $hobbies = UserHobby::where('uid', $user->id)->with('hobby')->get();
        return response()->json($hobbies);
    }

    public function saveHobbies(Request $request)
    {
        $user = $request->get('user');
        UserHobby::where('uid', $user->id)->delete();
        foreach ($request->all() as $hobby) {
            UserHobby::create([
                'uid' => $user->id,
                'hobby' => $hobby['hobby']['id'],
                'skill_scale' => $hobby['skill_scale'],
                'match_scale' => $hobby['match_scale'],
                'teach_scale' => $hobby['teach_scale']
            ]);
        }
        return response()->json(true);
    }

    public function getCauses(Request $request)
    {
        $user = $request->get('user');
        $causes = DB::table('user_causes')->where('uid', $user->id)->get();
        return response()->json($causes);
    }

    public function saveCauses(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_causes')->where('uid', $user->id)->delete();
        foreach ($request->all() as $cause) {
            if (isset($cause['cause'])) {
                DB::table('user_causes')->insert([
                    'uid' => $user->id,
                    'cause' => $cause['cause'],
                    'scale' => 1
                ]);
            }
        }
        return response()->json(true);
    }

    public function getSchool(Request $request)
    {
        $user = $request->get('user');
        $school = DB::table('user_schools')->where('uid', $user->id)->first();
        return response()->json($school);
    }

    public function getCollege(Request $request)
    {
        $user = $request->get('user');

        $acct = User::where('id','=',$user->id)->first();

        $colleges = json_decode($acct->college,true);
        $data = array();
        foreach($colleges as $college => $id){
            $data[$college] = College::where('id','=',$id)->first();
        }
        
        return response()->json($data);
    }
    public function saveCollege(Request $request)
    {
        $user = $request->get('user');

        User::where('id','=',$user->id)->update(['college' => json_encode($request->college)]);
        
        return response()->json(true);
    }

    public function saveSchool(Request $request)
    {
        $user = $request->get('user');
        DB::table('user_schools')->updateOrInsert(
            [
                'uid' => $user->id
            ],
            [
                'member' => $request->has('member') ? $request['member'] : null,
                'satis_level' => $request->has('member') && $request['member'] && $request->has('satis_level') ? $request['satis_level'] : null
            ]
        );
        return response()->json(true);
    }

    public function getPSProfileCompleted(Request $request)
    {
        $user = $request->get('user');
        $completes = [];
        if (DB::table('user_athletes')->where('uid', $user->id)->count() > 0) {
            $completes['athletes'] = true;
        } else {
            $completes['athletes'] = false;
        }
        if (DB::table('user_degrees')->where('uid', $user->id)->count() > 0) {
            $completes['degrees'] = true;
        } else {
            $completes['degrees'] = false;
        }
        if (DB::table('user_orgs')->where('uid', $user->id)->count() > 0) {
            $completes['orgs'] = true;
        } else {
            $completes['orgs'] = false;
        }
        if (DB::table('users')->whereNotNull('college')->count() > 0) {
            $completes['colleges'] = true;
        } else {
            $completes['colleges'] = false;
        }
        return response()->json($completes);
    }

    public function getCLProfileCompleted(Request $request)
    {
        $user = $request->get('user');
        $completes = [];
        if (DB::table('user_activities')->where('uid',$user->id)->count() > 0) {
            $completes['activities'] = true;
        } else {
            $completes['activities'] = false;
        }
        if (DB::table('user_causes')->where('uid', $user->id)->count() > 0) {
            $completes['causes'] = true;
        } else {
            $completes['causes'] = false;
        }
        if (
            DB::table('user_ethnicities')->where('uid', $user->id)->count() > 0 &&
            DB::table('user_gender_ages')->where('uid', $user->id)->count() > 0
        ) {
            $completes['gae'] = true;
        } else {
            $completes['gae'] = false;
        }
        if (DB::table('user_hobbies')->where('uid', $user->id)->count() > 0) {
            $completes['hobbies'] = true;
        } else {
            $completes['hobbies'] = false;
        }
        if (DB::table('user_homes')->where('uid', $user->id)->count() > 0) {
            $completes['home'] = true;
        } else {
            $completes['home'] = false;
        }
        if (DB::table('user_hometowns')->where('uid', $user->id)->count() > 0) {
            $completes['hometown'] = true;
        } else {
            $completes['hometown'] = false;
        }
        if (DB::table('user_learn_language_scales')->where('uid', $user->id)->count() > 0) {
            $completes['learn'] = true;
        } else {
            $completes['learn'] = false;
        }
        if (DB::table('user_relationships')->where('uid', $user->id)->count() > 0) {
            $completes['relationship'] = true;
        } else {
            $completes['relationship'] = false;
        }
        if (DB::table('user_religions')->where('uid', $user->id)->count() > 0) {
            $completes['religion'] = true;
        } else {
            $completes['religion'] = false;
        }
        if (DB::table('user_schools')->where('uid', $user->id)->count() > 0) {
            $completes['school'] = true;
        } else {
            $completes['school'] = false;
        }
        if (DB::table('user_speak_languages')->where('uid', $user->id)->count() > 0) {
            $completes['speak'] = true;
        } else {
            $completes['speak'] = false;
        }
        if (DB::table('user_work_careers')->where('uid', $user->id)->count() > 0) {
            $completes['career'] = true;
        } else {
            $completes['career'] = false;
        }
        return response()->json($completes);
    }

    public function getProfileCompleted(Request $request)
    {
        $user = $request->get('user');
        $count = 0;
        $uncompleted_items = [];
        if (DB::table('user_athletes')->where('uid', $user->id)->count() > 0) {
            $count++;
        } else {
            array_push($uncompleted_items, 'Athletes');
        }
        if (DB::table('user_causes')->where('uid', $user->id)->count() > 0) {
            $count++;
        } else {
            array_push($uncompleted_items, 'Causes');
        }
        if (DB::table('user_degrees')->where('uid', $user->id)->count() > 0) {
            $count++;
        } else {
            array_push($uncompleted_items, 'Degree');
        }
        if (DB::table('user_ethnicities')->where('uid', $user->id)->count() > 0) {
            $count++;
        } else {
            array_push($uncompleted_items, 'Ethnicity');
        }
        if (DB::table('user_gender_ages')->where('uid', $user->id)->count() > 0) {
            $count++;
        } else {
            array_push($uncompleted_items, 'Gender-Age');
        }
        if (DB::table('user_hobbies')->where('uid', $user->id)->count() > 0) {
            $count++;
        } else {
            array_push($uncompleted_items, 'Hobbies');
        }
        if (DB::table('user_homes')->where('uid', $user->id)->count() > 0) {
            $count++;
        } 
        // else {
        //     array_push($uncompleted_items, 'Home Base Location');
        // }
        if (DB::table('user_hometowns')->where('uid', $user->id)->count() > 0) {
            $count++;
        } 
        // else {
        //     array_push($uncompleted_items, 'Hometown');
        // }
        if (DB::table('user_learn_language_scales')->where('uid', $user->id)->count() > 0) {
            $count++;
        } 
        // else {
        //     array_push($uncompleted_items, 'Language Learning');
        // }
        if (DB::table('user_orgs')->where('uid', $user->id)->count() > 0) {
            $count++;
        } else {
            array_push($uncompleted_items, 'Organization');
        }
        if (DB::table('user_relationships')->where('uid', $user->id)->count() > 0) {
            $count++;
        } else {
            array_push($uncompleted_items, 'Relationship');
        }
        if (DB::table('user_religions')->where('uid', $user->id)->count() > 0) {
            $count++;
        } else {
            array_push($uncompleted_items, 'Religion');
        }
        if (DB::table('user_schools')->where('uid', $user->id)->count() > 0) {
            $count++;
        } 
        // else {
        //     array_push($uncompleted_items, 'School Related Question');
        // }
        if (DB::table('user_speak_languages')->where('uid', $user->id)->count() > 0) {
            $count++;
        } else {
            array_push($uncompleted_items, 'Language Speaking');
        }
        if (DB::table('user_work_careers')->where('uid', $user->id)->count() > 0) {
            $count++;
        } else {
            array_push($uncompleted_items, 'Work and Careers');
        }
        $percent = $count / 15 * 100;
        if(empty($uncompleted_items) && !is_null($user->email)){
            $user->notify(new SendWelcomeEmail());
        }
        return response()->json(['percent' => $percent, 'uncompleted' => $uncompleted_items]);
    }

    public function activateUser(Request $request)
    {
        $user = $request->get('user');

        $user->update([
            'activated_at' => Carbon::now(),
            'verified_at' => Carbon::now()
        ]);

        $data = [
            'uid' => $user->id
        ];
        event(new NewJoined($data));
        return response()->json(true);
    }

    public function changeLocationShow(Request $request, $show)
    {
        UserCoords::where('uid', $request->get('user')->id)->update(['show' => $show]);
        return response()->json('Location privacy has been changed successfully.');
    }

    public function getLocation(Request $request)
    {
        $coords = UserCoords::where('uid', $request->get('user')->id)->first();
        return response()->json($coords);
    }

    public function updateLocation(Request $request)
    {
        UserCoords::where('uid', $request->get('user')->id)->update([
            'radius' => $request['radius'],
            'lat' => $request['lat'],
            'lng' => $request['lng']
        ]);
        if (!is_null($request['lat']) && !is_null($request['lng'])) {
            $data = [
                'uid' => $request->get('user')->id
            ];
            event(new LocationUpdated($data));
        }
        return response()->json(true);
    }

    public function saveDeviceToken(Request $request, $deviceToken)
    {
        $user = $request->get('user');
        $userTokens = DB::table('user_devices')->where('uid', $user->id)->first();
        if (is_null($userTokens)) {
            DB::table('user_devices')->insert(['uid' => $user->id, 'tokens' => $deviceToken]);
        } else if (is_null($userTokens->tokens) || $userTokens->tokens == '') {
            DB::table('user_devices')->where('uid', $user->id)->update(['tokens' => $deviceToken]);
        } else {
            if (strpos($userTokens->tokens, $deviceToken) === false) {
                $tokenArr = explode(',', $userTokens->tokens);
                if (count($tokenArr) > 1) {
                    $userTokens->tokens = end($tokenArr);
                }
                $tokensStr = $userTokens->tokens . ',' . $deviceToken;
                DB::table('user_devices')->where('uid', $user->id)->update(['tokens' => $tokensStr]);
            }
        }
        return response()->json($userTokens);
    }

    public function uploadAvatar(Request $request)
    {
        $user = $request->get('user');
        $data = base64_decode($request['data']);
        Storage::disk('avatar')->put('avatar_' . $user->id . '.jpg', $data);
        $avararPath = config('app.url') . '/images/avatar/avatar_' . $user->id . '.jpg';
        User::where('id', $user->id)->update(['avatar' => $avararPath]);
        return response()->json($avararPath);
    }

    public function getInviteCode(Request $request)
    {
        $user = $request->get('user');
        $inviteCode = DB::table('user_invite_codes')->where('uid', $user->id)->first();
        if (is_null($inviteCode)) {
            $code = $this->buildInviteCode($user->id);
            return response()->json(['code' => $code['code'], 'expired' => $code['expired']]);
        } else {
            $expired = Carbon::parse($inviteCode->updated_at)->add('minute', 15);
            if ($expired < Carbon::now()) {
                $code = $this->buildInviteCode($user->id);
                return response()->json(['code' => $code['code'], 'expired' => $code['expired']]);
            } else {
                return response()->json(['code' => $inviteCode->code, 'expired' => $expired]);
            }
        }
    }

    public function generateInviteCode(Request $request)
    {
        $user = $request->get('user');
        $code = $this->buildInviteCode($user->id);
        return response()->json(['code' => $code['code'], 'expired' => $code['expired']]);
    }

    private function buildInviteCode($uid)
    {
        do {
            $six_digit_random_number = mt_rand(100000, 999999);
            $is_exist = DB::table('user_invite_codes')->where('code', $six_digit_random_number)->exists();
        } while ($is_exist);
        DB::table('user_invite_codes')->updateOrInsert(
            [
                'uid' => $uid
            ],
            [
                'code' => $six_digit_random_number,
                'updated_at' => Carbon::now()
            ]
        );
        return [
            'code' => $six_digit_random_number,
            'expired' => Carbon::parse(Carbon::now())->add('hour', 24)
        ];
    }

    public function sendTestPush() 
    {
        echo config('app.url');
    }

    public function updateUserActivity(Request $request)
    {
        $user = $request->get('user');

        $userActivity = UserActivity::firstOrNew(['uid'=>$user->id]);
        $userActivity->parents_from_alma = $request->parents_from_alma;
        $userActivity->siblings_from_alma = $request->siblings_from_alma;
        $userActivity->play_video_games = $request->play_video_games;
        $userActivity->military_rank = $request->military_rank;
        $userActivity->military_code = $request->military_code;
        $userActivity->video_games_frequency = $request->video_games_frequency;
        $userActivity->video_games_categories = $request->video_games_categories;
        $userActivity->video_games_fav_title = serialize($request->video_games_fav_title);
        $userActivity->athletic_stuff_you_play = serialize($request->athletic_stuff_you_play);
        $userActivity->have_exotic_pet = $request->have_exotic_pet;
        $userActivity->havePet = $request->havePet;
        $userActivity->pet = $request->pet;
        $userActivity->fan_of_alma_football = $request->fan_of_alma_football;
        $userActivity->fan_of_alma_basketball = $request->fan_of_alma_basketball;
        $userActivity->in_us_military = $request->in_us_military;
        $userActivity->military_type = $request->military_type;
        $userActivity->dependent_us_military_person = $request->dependent_us_military_person;
        $userActivity->instrument = $request->instrument;
        $userActivity->long_have_lived_here = $request->long_have_lived_here;
        $userActivity->country_to_travel = $request->country_to_travel;
        $userActivity->state_to_travel = $request->state_to_travel;
        $userActivity->city_to_travel = $request->city_to_travel;
        $userActivity->uid = $user->id;
        $userActivity->save();

        $userActivity->video_games_fav_title = unserialize($userActivity->video_games_fav_title);
        $userActivity->athletic_stuff_you_play = unserialize($userActivity->athletic_stuff_you_play);

        return response()->json($userActivity);
    }

    public function getUserActivity(Request $request)
    {
        $user = $request->get('user');

        $userActivity = UserActivity::where('uid', $user->id)->first();

        if($userActivity->video_games_fav_title) {
            $userActivity->video_games_fav_title = unserialize($userActivity->video_games_fav_title);
        }
    
        
        if($userActivity->athletic_stuff_you_play) {
            $userActivity->athletic_stuff_you_play = unserialize($userActivity->athletic_stuff_you_play);
        }

        return response()->json($userActivity);
    }

    public function getCompanies(Request $request) {
        $user = $request->get('user');
        $companies = $user->company_created;
        foreach ($companies as $company) {
            foreach ($company->ads as $ad){
                $ad = $this->returnLeadsUsed($ad);
            }
        }
        return response()->json($companies);
    }
    public function deleteUser(Request $request) {
        $user = $request->get('user');

        //delete user content
        redirect()->action('Api\PostController@deleteAllPosts');
        redirect()->action('Api\MessageController@deleteAllMessagesByUID');
        redirect()->action('Api\CompanyController@delete');

        //delete user info
        User::where('id', $user->id)->first()->delete();

        return response()->json(true);
    }
}
