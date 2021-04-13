<?php

namespace App\Http\Requests;

use App\Http\Requests\APIRequest;

class CompanyRequest extends APIRequest
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
            'companyName' => 'required|string',
            'creatorTitle' => 'required|string',
            'companyStartedOn' => 'required|date',
            'description' => 'required|string',
            'paid' => 'required|boolean',
            'websiteLink' => 'nullable|string',
            'videoLink' => 'nullable|string',
            'photoUrl' => 'nullable|string'
        ];
    }
    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */
    public function messages()
    {
        return [
            'companyName.required' => 'A company name is required.',
            'creatorTitle.required' => 'A creator title is required.',
            'companyStartedOn.required' => 'A Start Date is required.',
            'description.required' => 'A company description is required.',
            'paid.required' => 'A payment status is required.'
        ];
    }
}
