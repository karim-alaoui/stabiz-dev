<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam logo file max 4 mb. Types are jpg, png, bmp. If you just want to delete the dp, send `null` to remove the existing photo No-example
 * @bodyParam banner file max 4 mb. Types are jpg, png, bmp. If you just want to delete the dp, send `null` to remove the existing photo No-example
 */
class UpdateComImgReq extends FormRequest
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
            'logo' => ['nullable', 'file', 'max:4000', 'mimes:jpg,jpeg,png,bmp'],
            'remove_banner' => ['nullable', 'bool'],
            'remove_logo' => ['nullable', 'bool'],
            'banner' => ['nullable', 'file', 'max:4000', 'mimes:jpg,jpeg,png,bmp']
        ];
    }

    public function messages()
    {
        return [
            'logo.max' => __('Logo file size is 4mb max'),
            'banner.max' => __('Banner file size is 4mb max'),
            'logo.mimes' => __('The image has to either JPG, JPEG, PNG or bmp')
        ];
    }
}
