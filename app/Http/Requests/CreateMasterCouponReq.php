<?php

namespace App\Http\Requests;

use App\Models\MasterCoupon;
use App\Rules\ValidateDateTime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @bodyParam redeem_by string date time. Till this **UTC** datetime this coupon will be valid No-example
 */
class CreateMasterCouponReq extends FormRequest
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
            'name' => ['required', 'string', 'unique:' . (new MasterCoupon())->getTable() . ',name', 'max:20'],
            'amount_off' => ['required_without:percent_off', 'integer'],
            'percent_off' => ['required_without:amount_off', 'integer', 'min:1', 'max:100'],
            'duration' => ['required', Rule::in(['forever', 'once', 'repeating'])],
            'duration_in_months' => ['required_if:duration,repeating', 'integer', 'max:12', 'min:1'],
            'redeem_by' => ['nullable', new ValidateDateTime()],
            'assign_after' => ['required_without:is_a_campaign', Rule::in(['registration', 'profile_fill_up'])],
            'is_a_campaign' => ['required_without:assign_after', 'boolean']
        ];
    }
}
