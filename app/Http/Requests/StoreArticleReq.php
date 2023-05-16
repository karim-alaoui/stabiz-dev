<?php

namespace App\Http\Requests;

use App\Models\Category4Article;
use App\Models\Industry;
use App\Rules\ValidateDateTime;
use App\Rules\ValidateEmptyHTML;
use App\Rules\ValuesExist;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam publish_after string UTC date time after today. Default value is `now`. After this datetime, the item will be shown to readers No-example
 * @bodyParam hide_after string UTC date time. After this datetime, it will be hidden from users No-example
 * @bodyParam is_draft boolean if to put into draft or not No-example
 * @bodyParam audience array required the array can have either `entrepreneur` or `founder` or both No-example
 * @bodyParam tags [] tags name max 20 No-example
 * @bodyParam category_ids array ids of category from category for article endpoint No-example
 * @bodyParam industry_ids array ids of industries No-example
 */
class StoreArticleReq extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // TODO: put filter in search using package subscription
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
            'title' => ['required', 'string', 'max:400'],
            'description' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string', new ValidateEmptyHTML()],
            'publish_after' => ['nullable', 'after_or_equal:today', new ValidateDateTime()],
            'hide_after' => ['nullable', 'after:show_after', new ValidateDateTime()],
            'is_draft' => ['nullable', 'boolean'],
            'audience' => ['bail', 'required', 'array', function ($attribute, $value, $fail) {
                $valid = ['entrepreneur', 'founder'];
                array_map(function ($val) use ($valid, $fail) {
                    if (!in_array($val, $valid)) $fail(__('Valid values are entrepreneur, founder'));
                }, $value);
            }],
            'tags' => ['bail', 'nullable', 'array', 'min:1', 'max:20', function ($attribute, $value, $fail) {
                if (gettype($value) != 'array') $fail(__('validation.array', ['attribute' => $attribute]));
                if (gettype($value) == 'array') {
                    foreach ($value as $item) {
                        if (strlen($item) > 25) $fail(__('A tag can not be more than 25 characters'));
                    }
                }
            }],
            'category_ids' => [
                'bail',
                'nullable',
                'array',
                'min:1',
                'max:10',
                new ValuesExist('id', Category4Article::class),
            ],
            'industry_ids' => [
                'bail',
                'nullable',
                'array',
                'min:1',
                'max:10',
                new ValuesExist('id', Industry::class)
            ]
        ];
    }
}
