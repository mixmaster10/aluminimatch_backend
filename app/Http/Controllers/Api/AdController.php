<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdRequest;
use App\Models\Ad;
use App\Models\Company;
use App\Traits\AdsResponseTrait;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    use AdsResponseTrait;
    //

    public function index() {
        $ads = Ad::leftJoin('companies',function($join) {
                $join->on('companies.id','=','ads.company_id');
            })->select('ads.*','companyName','companies.photoUrl as companyPhotoUrl')->where('ads.active',1)->get();
        foreach ($ads as $ad) {
            $ad = $this->returnLeadsUsed($ad);
        }
        return response()->json($ads);
    }

    public function show(Ad $ad) {
        return response()->json($this->returnLeadsUsed($ad));
    }

    public function store(AdRequest $request, Company $company) {
        $data = $request->validated();
        $data['company_id'] = $company->id;
        // dd($data);
        if (!isset($data['leadsUsed']) || is_null($data['leadsUsed']) || empty($data['leadsUsed'])) {
            $data['leadsUsed'] = json_encode([
                'likes' => [],
                'comments' => [],
                'viewed_sponsor' => []
            ]);
        } else {
            $data['leadsUsed'] = json_encode($data['leadsUsed']);
        }
        $ad = Ad::create($data);
        return response()->json($this->returnLeadsUsed($ad));
    }

    public function update(Request $request, Ad $ad) {
        $data = $request->all();
        if (!isset($data['leadsUsed']) || is_null($data['leadsUsed']) || empty($data['leadsUsed'])) {
            $data['leadsUsed'] = json_encode([
                'likes' => [],
                'comments' => [],
                'viewed_sponsor' => []
            ]);
        } else {
            $data['leadsUsed'] = json_encode($data['leadsUsed']);
        }
        $ad->update($data);        
        return response()->json($this->returnLeadsUsed($ad));
    }

    public function uploadAdPhoto(Request $request, Ad $ad){
        $company = $ad->company;
        $photo = base64_decode($request['photo']);
        Storage::disk('photo')->put('/company_'.$company->id.'/ads/ad_photo_'.$ad->id.'.jpg', $photo);
        $photoUrl = config('app.url').'/images/photo/company_'.$company->id.'/ads/ad_photo_'.$ad->id.'.jpg';
        $ad->update(['photoUrl'=>$photoUrl]);
        return response()->json($photoUrl);
    }
}
