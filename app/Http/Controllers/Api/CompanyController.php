<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Traits\AdsResponseTrait;
use App\Traits\MatchTrait;
use Illuminate\Foundation\Console\Presets\React;

class CompanyController extends Controller
{
    use AdsResponseTrait,MatchTrait;
    //
    public function index(Request $request) {
        $user = $request->get('user');
        $companies = Company::where('paid',1)->get();
        foreach ($companies as $company) {
            foreach ($company->ads as $ad){
                $ad = $this->returnLeadsUsed($ad);
            }
            $company->match = $this->getMatchPercent($user->id,$company->creator_id);
        }
        return response()->json($companies);
    }

    public function show(Request $request,Company $company) {
        $user = $request->get('user');
        $company->match = $this->getMatchPercent($user->id,$company->creator_id);
        foreach ($company->ads as $ad){
            $ad = $this->returnLeadsUsed($ad);
        }
        return response()->json($company);
    }

    public function store(CompanyRequest $request) {
        $user = $request->get('user');
        $company_info = $request->validated();
        $company_info['creator_id'] = $user->id;
        if ($company_info['paid']) {
            $company_info['leadsBalance'] = 10;
        } else {
            $company_info['leadsBalance'] = 0;
        }
        $company = Company::create($company_info);
        DB::table('user_company')->insert([
            'user_id' => $user->id,
            'company_id' => $company->id
        ]);
        $response = $this->createResponseArray($user);
        return response()->json($response);
    }

    public function update(Request $request, Company $company) {
        $user = $request->get('user');
        // if ($request['paid'])
        //     $request['leadsBalance'] = 10;
        $company->update($request->all());
        $company->match = $this->getMatchPercent($user->id,$company->creator_id);
        // $response = $this->createResponseArray($user);
        return response()->json($company);
    }

    public function delete(Request $request){
        $user = $request->get('user');
        $company = Company::where('creator_id','=',$user->id)->first();
        $company->ads()->delete();
        $company->delete();
        return response()->json(true);
    }

    public function uploadCompanyPhoto(Request $request, Company $company){
        $user = $request->get('user');
        $photo = base64_decode($request['photo']);
        Storage::disk('photo')->put('/company_'.$company->id.'/company_photo_'.$company->id.'.jpg', $photo);
        $photoUrl = config('app.url').'/images/photo/company_'.$company->id.'/company_photo_'.$company->id.'.jpg';
        $company->update(['photoUrl'=>$photoUrl]);
        return response()->json($photoUrl);
    }

    public function createResponseArray(User $user) {
        $i = 0;
        $response = array();
        foreach ($user->company_created as $company) {
            $response['companies'][$i] = $company->toArray();
            foreach($company->ads as $ad){
                $ad = $this->returnLeadsUsed($ad);
                $response['companies'][$i]['ads'][] = $ad->toArray();
            }
            $company->match = $this->getMatchPercent($user->id,$company->creator_id);
            $i++;
        }
        return $response;
    }
}
