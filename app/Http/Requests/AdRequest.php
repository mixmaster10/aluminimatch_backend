<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdRequest  extends APIRequest
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
            'title' => 'required|string',
            'description' => 'required|string',
            'active' => 'required|boolean',
            'leadsRemaining' => 'nullable|integer',
            'totalLeads' => 'nullable|integer',
            'comment_count' => 'nullable|integer',
            'isLiked' => 'nullable|boolean',
            'likes_count' => 'nullable|integer',
            'websiteLink' => 'nullable|string',
            'audience' => 'nullable|string',
            'photoUrl' => 'nullable|string',
            'leadsUsed' => 'nullable'
        ];
    }

    public function messages() {
        return [
            'title.required' => "An Ad Title is required.",
            'description.required' => 'An Ad Description is required.',
            'active.requried' => 'An Add status is required'
        ];
    }
}
