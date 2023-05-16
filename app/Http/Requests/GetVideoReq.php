<?php

namespace App\Http\Requests;

use App\Actions\PaginationRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @queryParam page current page number for pagination Example: 2
 * @queryParam per_page the number of results to return per request. Default value is 15 Example: 15
 */
class GetVideoReq extends FormRequest
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
            'title' => ['nullable', 'string']
        ]);
    }
}
