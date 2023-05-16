<?php

namespace App\Http\Requests;

use App\Models\Staff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @bodyParam role string required either of `super-admin` or `matchmaker` case sensitive  No-example
 */
class CreateStaffReq extends FormRequest
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
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => ['required', 'email', 'unique:' . (new Staff())->getTable() . ',email', 'max:50'],
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:password',
            'phone' => 'required|string|max:11|min:10',
            'role' => [
                'required',
                Rule::in([Staff::SUPER_ADMIN_ROLE, Staff::MATCH_MAKER_ROLE])
            ]
        ];
    }
}
