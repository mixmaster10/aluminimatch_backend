<?php

namespace App\Http\Requests;

use App\Http\Requests\APIRequest;

class EventRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'category_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'minimum_required' => 'required|integer',
            'max_needed' => 'required|integer',
            'active' => 'required|boolean',
            'start_at' => 'required|date_format:Y-m-d H:i:s',
            'end_at' => 'required|date_format:Y-m-d H:i:s',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'zip_code' => 'required|string',
            'meeting_type' => 'required|string',
            'meeting_link' => 'nullable|string',
            'meeting_id' => 'required|string',
            'meeting_passcode' => 'nullable|string',
            'number_of_participants' => 'required|integer',
            'rsvp_yes' => 'nullable|integer',
            'rsvp_interested' => 'nullable|integer',
        ];
    }
}
