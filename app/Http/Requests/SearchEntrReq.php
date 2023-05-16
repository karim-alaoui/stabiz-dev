<?php

namespace App\Http\Requests;

use App\Actions\PaginationRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @queryParam expecting_income income range id No-example
 * @queryParam area_id No-example
 * @queryParam prefecture_id No-example
 * @queryParam industries comma separated industry id Eg `2,3,4` No-example
 * @queryParam No-example
 */
class SearchEntrReq extends FormRequest
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
    public function rules()
    {
        return array_merge(PaginationRules::execute(), [
            'expecting_income' => ['nullable', 'integer']
        ]);
    }
}
