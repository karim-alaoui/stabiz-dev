<?php

namespace App\Http\Requests;

use App\Actions\PaginationRules;
use App\Rules\ValidateDateTime;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @queryParam show_after UTC date No-example
 * @queryParam hide_after UTC date No-example
 * @queryParam id No-example
 * @queryParam query this will search everything. From title to description to everything No-example
 * @queryParam page current page number for pagination Example: 2
 * @queryParam per_page the number of results to return per request. Default value is 15 Example: 15
 */
class GetTopicReq extends FormRequest
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
            'id' => ['nullable', 'integer'],
            'query' => ['nullable', 'string'],
            'show_after' => ['nullable', new ValidateDateTime()],
            'hide_after' => ['nullable', new ValidateDateTime()],
        ]);
    }
}
