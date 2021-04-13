<?php

namespace App\Traits;

use App\Models\User;

trait AdsResponseTrait
{
    protected function success($message, User $user, $status = 200)
    {
        $user_response = $user->toArray();
        $i = 0;
        // dd($user->company_created);
        foreach ($user->company_created as $company) {
            $user_response['companies'][$i] = $company->toArray();
            foreach($company->ads as $ad){
                $user_response['companies'][$i]['ads'][] = $ad->toArray();
            }
            $i++;
        }
        return response([
            'success' => true,
            'user' => $user_response,
            'message' => $message,
        ], $status);
    }

    protected function failure($message, $status = 422)
    {
        return response([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    public function returnLeadsUsed($ad) {
        if (is_null($ad->leadsUsed)) {
            $ad->leadsUsed = [
                'likes' => [],
                'comments' => [],
                'viewed_sponsor' => []
            ];
        } else {
            $ad->leadsUsed = json_decode($ad->leadsUsed);
        }
        return $ad;
    }
}