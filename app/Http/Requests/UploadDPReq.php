<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam photo file max 4 mb. Types are jpg, png, bmp. If you just want to delete the dp, send `null` to remove the existing photo No-example
 */
class UploadDPReq extends FormRequest
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
            'photo' => ['nullable', 'file', 'max:4000', 'mimes:jpg,jpeg,png,bmp']
        ];
    }

    public function messages()
    {
        return [
            'photo.max' => __('Max file size is 4mb'),
            'photo.mimes' => __('File can be either JPG, JPEG, PNG or bmp')
        ];
    }
}
