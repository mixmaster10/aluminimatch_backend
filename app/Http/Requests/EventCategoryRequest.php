<?php

namespace App\Http\Requests;

use App\Http\Requests\APIRequest;

class EventCategoryRequest extends APIRequest
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
            'parent_id' => 'nullable|integer',
            'title' => 'string'
        ];
    }
}
