<?php

namespace App\Http\Controllers\Api;

use App\Models\Athlete;
use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Country;
use App\Models\Degree;
use App\Models\Ibc;
use App\Models\Industry;
use App\Models\Organization;
use App\Models\State;
use App\Models\Hobby;
use Illuminate\Http\Request;

class BaseDataController extends Controller
{
    function getHobbiesData() {
        $hobbies = Hobby::all();
        return response()->json(compact('hobbies'));
    }

    function getCountriesWithOUCollege() {
        $countries = Country::all();
        $states = State::where('country_id', 1)->get();
        $colleges = College::where('state_id',36)->select(['id', 'name'])->get();
        return response()->json(compact('countries', 'states', 'colleges'));
    }

    function getCountries() {
        $countries = Country::all();
        return response()->json($countries);
    }

    function getStates() {
        $states = State::all();
        return response()->json($states);
    }

    function filterState(Request $request) {
        $states = State::where('country_id', $request->query('country'))->get();
        return response()->json($states);
    }

    function getColleges() {
        $athletes = College::select(['id', 'country_id', 'state_id', 'name'])->get();
        return response()->json($athletes);
    }

    function filterColleges(Request $request) {
        $country_id = $request->query('country');
        if ($country_id == 1) {
            $state_id = $request->query('state');
            $colleges = College::where('state_id', $state_id)->select(['id', 'name'])->get();
        } else {
            $colleges = College::where('country_id', $country_id)->select(['id', 'name'])->get();
        }
        return response()->json($colleges);
    }

    function getCollegeInfo(Request $request) {
        $id = $request['cid'];
        $college = College::find($id);
        return response()->json($college);
    }

    function getDegrees() {
        $degrees = Degree::all();
        return response()->json($degrees);
    }

    function getAthletes() {
        $athletes = Athlete::all();
        return response()->json($athletes);
    }

    function getOrganizations() {
        $orgs = Organization::all();
        return response()->json($orgs);
    }

    function getIBCs() {
        $ibcs = Ibc::all();
        return response()->json($ibcs);
    }

    function getIndustries() {
        $industries = Industry::all();
        return response()->json($industries);
    }

    function getPSData() {
        $degrees = Degree::all();
        $orgs = Organization::all();
        $athletes = College::select(['id', 'country_id', 'state_id', 'name'])->get();
        return response()->json(compact('degrees', 'orgs', 'athletes'));
    }

}
