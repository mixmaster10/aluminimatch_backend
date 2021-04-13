<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Event;

class EventController extends Controller
{
    //
    public function index() {
        $event = Event::where('active',true)->get();
        return response()->json($event);
    }

    public function show(Request $request,Event $event) {
        return response()->json($event);
    }

    public function store(EventRequest $request) {
        // dd($request);
        $user = $request->get('user');
        $event_info = $request->validated();
        $event_info['creator_id'] = $user->id;
        $event = Event::create($event_info);
        return response()->json($event);
    }

    public function update(Request $request,Event $event) {
        $event->update($request->all());
        return response()->json($event);
    }
}
