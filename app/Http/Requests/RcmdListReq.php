<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @queryParam user_id required string the user id of the user whom you want to recommend No-example
 * @queryParam name No-example
 * @queryParam age_greater_than No-example
 * @queryParam age_lower_than No-example
 * @queryParam gender No-example
 * @queryParam school_name No-example
 * @queryParam school_major No-example
 * @queryParam present_company No-example
 * @queryParam working_status_id No-example
 * @queryParam occupation_id No-example
 * @queryParam present_post_id No-example
 * @queryParam transfer either of `yes`, `no`, `only domestic`, `only overseas` No-example
 * @queryParam management_exp_id No-example
 * @queryParam pfd_industry_ids string id seperated by comma No-example
 * @queryParam pfd_position_ids string id seperated by comma No-example
 * @queryParam pfd_prefecture_ids string id seperated by comma No-example
 * @queryParam expected_income_range_id income range ID No-example
 */
class RcmdListReq extends FormRequest
{
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
            //currently this request is only for founder user. This is the founder user id
            'user_id' => ['required', 'integer', 'exists:' . (new User())->getTable() . ',id'],
        ];
    }
}
