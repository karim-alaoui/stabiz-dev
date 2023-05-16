<?php

namespace App\Http\Requests;

use App\Actions\MakeEditRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

/**
 * @bodyParam role string either of `super-admin` or `matchmaker` No-example
 */
class UpdateStaffReq extends FormRequest
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
        $rules = (new CreateStaffReq())->rules();
        $rules = Arr::except($rules, ['password', 'confirm_password']);
        return MakeEditRules::execute($rules);
    }
}
