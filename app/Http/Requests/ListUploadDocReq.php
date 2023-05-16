<?php

namespace App\Http\Requests;

use App\Actions\PaginationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @queryParam user_id No-example
 * @queryParam state either `approved` or `rejected` or `not touched`. `Not touched` means neither approved nor rejected No-example
 * @queryParam page current page number for pagination Example: 2
 * @queryParam per_page the number of results to return per request. Default value is 15 Example: 15
 */
class ListUploadDocReq extends FormRequest
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
    public function rules(): array
    {
        return array_merge(PaginationRules::execute(), [
            'state' => ['nullable', 'string', Rule::in('approved', 'rejected', 'not touched')]
        ]);
    }
}
