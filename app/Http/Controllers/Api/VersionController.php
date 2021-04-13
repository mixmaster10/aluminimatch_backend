<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class VersionController extends Controller
{
    public function getLatestVersion(Request $request) {
        $userver = DB::table('version')->where('appVersion','=',$request['appVersion'])->first();
        $latest = DB::table('version')->orderBy('id','desc')->first();
        
        if(!$userver){
            return response()->json([
                'appVersion' => $latest->appVersion,
                'update' => true,
                'force' => true
            ]);
        }
        
        $versions = DB::table('version')->where('id','>',$userver->id)->get();
        

        

        $flag = false;
        if(!count($versions) == 0) {
            foreach($versions as $ver){
                $flag = $flag || $ver->force;
            }
            return response()->json([
                'appVersion' => $latest->appVersion,
                'update' => true,
                'force' => $flag
            ]);
        }

        return response()->json([
            'appVersion' => $latest->appVersion,
            'update' => false,
            'force' => false
        ]);
    }
}
