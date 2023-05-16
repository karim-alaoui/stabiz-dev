<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @bodyParam user_type string required either `entrepreneur` or `founder` No-example
 */
class ResetPassReq extends FormRequest
{
    use ValidationTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'otp' => 'required',
            'email' => ['required', 'email'],
            'password' => $this->passwordValidation(),
            'confirm_password' => ['required', 'same:password'],
            'user_type' => ['required', Rule::in([User::FOUNDER, User::ENTR])]
        ];
    }

    public function messages(): array
    {
        return $this->passwordValidationMsg();
    }
}
