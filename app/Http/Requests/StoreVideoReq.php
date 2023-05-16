<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoReq extends FormRequest
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
            'title' => 'required|string|max:255',
            'link' => ['required', 'string', 'max:255', 'url', function ($attribute, $value, $fail) {
                if (!preg_match('/^(https?:\/\/)?(www.)?youtube.com/i', $value)) {
                    $fail(__('link has to be youtube link'));
                }
            }],
            'description' => 'required|string|max:500',
        ];
    }
}
