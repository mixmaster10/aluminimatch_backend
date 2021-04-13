<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MosImport;
use App\Models\Millitary;   
class MosController extends Controller
{
    public function import()
    {
        $data = Excel::import(new MosImport, request()->file('import_file'));
        return response()->json("Data import Successfully",200);

    }
    public function getMos()
    {
        $data = Millitary::all();
        return response()->json(compact('data'),200);
    }
    public function singleMos()
    {
        $data = Millitary::where('id',request()->id)->select('description','rank')->get();
        return response()->json(compact('data'),200);
    }
    public function singleMosByCode($code, $branch)
    {
        $data = Millitary::where('code','like','%'.$code.'%')
        ->where('military_branch',$branch)
        ->select('description','rank','id','code')->get();
        return response()->json(compact('data'),200);
    }
}
