<?php

namespace App\Http\Requests;

use App\Actions\PaginationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @queryParam type either `rejected` or `accepted` No-example
 * @queryParam page current page number for pagination Example: 2
 * @queryParam per_page the number of results to return per request. Default value is 15 Example: 15
 */
class FilterAplReq extends FormRequest
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
            'type' => ['nullable', Rule::in(['accepted', 'rejected'])]
        ]);
    }

    public function messages()
    {
        return [
            'type.in' => __('Either accepted or rejected value accepted')
        ];
    }
}
