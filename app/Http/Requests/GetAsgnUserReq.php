<?php

namespace App\Http\Requests;

use App\Actions\PaginationRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @queryParam id No-example
 * @queryParam user_id comma separated string like `1,2,3` No-example
 * @queryParam staff_id comma separated string like `1,2,3` Only allowed for super admin, other wise it would have no effect. For any other type of staff, it would return only the users assigned to that staff. No-example
 * @queryParam type either of `entrepreneur` or `founder`. It will return assigned users of that type. No-example
 * @queryParam first_name_u user first name No-example
 * @queryParam last_name_u user last name No-example
 * @queryParam first_name_s staff first name No-example
 * @queryParam last_name_s staff last name No-example
 * @queryParam email user email No-example
 * @queryParam gender_u user gender No-example
 * @queryParam page current page number for pagination Example: 2
 * @queryParam per_page the number of results to return per request. Default value is 15 Example: 15
 */
class GetAsgnUserReq extends FormRequest
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
        return array_merge(PaginationRules::execute(), [

        ]);
    }
}
