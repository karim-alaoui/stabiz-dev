<?php

namespace App\Http\Requests;

use App\Actions\PaginationRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @queryParam query this will query the id, title, content, description No-example
 * @queryParam draft to get items that are in the draft, send `true` or `false` as `string` No-example
 * @queryParam page current page number for pagination Example: 2
 * @queryParam per_page the number of results to return per request. Default value is 15 Example: 15
 */
class GetArticleReq extends FormRequest
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
            'query' => ['nullable', 'string'],
//            'publish_after' => ['nullable', new ValidDate()],
//            'hide_after' => ['nullable', new ValidDate()],
            'draft' => ['nullable', 'string'],
//            'audience' => ['nullable', Rule::in(['entrepreneur', 'founder', 'entrepreneur,founder'])]
        ]);
    }
}
