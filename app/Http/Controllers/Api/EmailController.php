<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\SendEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string',
            'subject' => 'required|string',
            'intro_line' => 'required|string',
            'message' => 'required|string',
        ]);
        if($validator->fails()){
            return $this->errorResponse($validator->messages(),422);
        } else {
            $user = $request->get('user');
            $user->forceFill($request->all())->notify(new SendEmail());
            return $this->successResponse(null,'sending email..');
        }

    }
}
