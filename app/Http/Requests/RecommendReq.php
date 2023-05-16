<?php

namespace App\Http\Requests;

use App\Actions\PaginationRules;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RecommendReq extends FormRequest
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
            'recommended_to_user_id' => ['required', 'integer', 'exists:' . (new User())->getTable() . ',id'],
            'recommended_user_id' => ['required', 'integer', 'exists:' . (new User())->getTable() . ',id'],
        ];
    }

    public function messages(): array
    {
        return array_merge(PaginationRules::execute(), [
            'recommended_user_id.required' => __('Please provide the user that you are recommending'),
            'recommended_to_user_id.required' => __('Please provide the user whom you are recommending to'),
        ]);
    }
}
