<?php

namespace App\Http\Requests;

use App\Rules\ValidateDateTime;
use App\Rules\ValidateEmptyHTML;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam show_after string UTC date time after today. Default value is `now`. After this datetime, the item will be shown to users No-example
 * @bodyParam hide_after string UTC date time. After this datetime, it will be hidden from users No-example
 */
class CreateNewsTopicReq extends FormRequest
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
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:100000', new ValidateEmptyHTML()],
            'visible_to' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $allowedValues = ['organizer', 'founder', 'entrepreneur', 'others'];
                    foreach ($value as $v) {
                        if (!in_array($v, $allowedValues)) {
                            $fail("The {$attribute} field must contain only valid values.");
                        }
                    }
                }
            ],                      
            'show_after' => ['nullable', 'after_or_equal:today', new ValidateDateTime()],
            'hide_after' => ['nullable', 'after:show_after', new ValidateDateTime()]
        ];
    }

    /**
     * Set the "visible_to" attribute on the model based on the request data.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setVisibleToAttribute($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->merge([
            'visible_to' => array_intersect($value, ['organizer', 'founder', 'entrepreneur', 'others'])
        ]);
    }
}
