<?php

namespace App\Traits;

use App\Models\UserCoords;

trait DistanceTrait
{
    public function getDistance($uid1, $uid2){
        $pos1 = UserCoords::where('uid', $uid1)->first();
        $pos2 = UserCoords::where('uid', $uid2)->first();
        if (is_null($pos1) || is_null($pos2)) {
            return null;
        }
        $R = 6371;
        $lat1 = deg2rad($pos1->lat);
        $lon1 = deg2rad($pos1->lng);
        $lat2 = deg2rad($pos2->lat);
        $lon2 = deg2rad($pos2->lng);
        $dlon = $lon2 - $lon1;
        $dlat = $lat2 - $lat1;
        $a = (pow(sin($dlat/2),2)) + cos($lat1) * cos($lat2) * pow(sin($dlon/2),2);
        $c = 2 * atan2( sqrt($a), sqrt(1-$a) ) ;
        $d = $R * $c;
        $distance = number_format((float)$d, 1, '.', '');
        return $distance;
    }


    public function buildDistanceQuery($query, $coords, $radius = null) {
        $haversine = "(6371 * acos(cos(radians($coords->lat)) 
                     * cos(radians(user_coords.lat)) 
                     * cos(radians(user_coords.lng) 
                     - radians($coords->lng)) 
                     + sin(radians($coords->lat)) 
                     * sin(radians(user_coords.lat))))";
        if (is_null($radius)) {
            return $query->selectRaw("{$haversine} AS distance");
        }
        return $query->selectRaw("{$haversine} AS distance")->whereRaw("{$haversine} < ?", [$radius]);
    }

    public function buildNormalDistanceQuery($query, $coords) {
        $query->join('user_coords', 'users.id', '=', 'user_coords.uid');
        $haversine = "(6371 * acos(cos(radians($coords->lat)) 
                     * cos(radians(user_coords.lat)) 
                     * cos(radians(user_coords.lng) 
                     - radians($coords->lng)) 
                     + sin(radians($coords->lat)) 
                     * sin(radians(user_coords.lat))))";
        return $query->selectRaw("{$haversine} AS distance");
    }
}
