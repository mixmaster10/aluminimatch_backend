<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PostReports;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $reported_posts = PostReports::orderBy('created_at')->with('reportedBy')->with('reportedPost')->get()->toArray();
        // dd($reported_posts);
        return view('home',compact('reported_posts'));
    }
}