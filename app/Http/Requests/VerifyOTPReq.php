<?php

namespace App\Http\Requests;

use App\Models\OTP;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyOTPReq extends FormRequest
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
        $exists = 'exists:' . (new OTP())->getTable() . ',email';
        return [
            'otp' => ['required', 'min:6', 'max:6'],
            'email' => ['required', 'string', 'email', $exists],
            'user_type' => ['required', Rule::in([User::FOUNDER, User::ENTR])]
        ];
    }
}
