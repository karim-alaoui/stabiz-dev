<?php

namespace App\Http\Requests;

use App\Models\Staff;
use App\Models\User;
use App\Rules\UserOrFounderProfileExists;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam user_id integer[] required array of user ids No-example
 * @bodyParam staff_id integer required No-example
 */
class AssignUserReq extends FormRequest
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
            'user_id' => ['required', 'integer', new UserOrFounderProfileExists],
            'staff_id' => ['required', 'integer', 'exists:' . (new Staff())->getTable() . ',id']
        ];
    }

}
