<?php

namespace App\Http\Requests;

use App\Actions\PaginationRules;
use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\Pure;

/**
 * @queryParam first_name No-example
 * @queryParam last_name No-example
 * @queryParam type either `founder` or `entrepreneur` if you want both types, send `founder,entrepreneur` Example: founder
 * @queryParam add_assigned_staff send `1` for `true` and it will add it No-example
 * @queryParam page current page number for pagination Example: 2
 * @queryParam per_page the number of results to return per request. Default value is 15 Example: 15
 */
class GetUsersByStaffReq extends FormRequest
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
    #[Pure] public function rules(): array
    {
        return array_merge(PaginationRules::execute(), [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'type' => ['required', 'string'],
            'add_assigned_staff' => ['nullable', 'bool']
        ]);
    }
}
