<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @queryParam industries industry id. comma separated string of industry id if you want to search for multiple No-example
 * @queryParam positions position id. comma separated string of position id if you want to search for multiple No-example
 * @queryParam expecting_income income range id No-example
 * @queryParam prefecture_id No-example
 * @queryParam area_id No-example
 * @queryParam page current page number for pagination Example: 2
 * @queryParam per_page the number of results to return per request. Default value is 15 Example: 15
 */
class SearchFdrReq extends FormRequest
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
        ];
    }
}
