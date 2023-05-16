<?php

namespace App\Http\Requests;

use App\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\Pure;

class RegReq extends FormRequest
{
    use ValidationTrait;

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
    #[Pure] public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => $this->passwordValidation(),
//            'income_range_id' => ['required', 'integer', 'exists:' . (new IncomeRange())->getTable() . ',id']
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'email' => $this->email ? strtolower($this->email) : $this->email
        ]);
    }

    public function messages(): array
    {
        return array_merge($this->passwordValidationMsg(), []);
    }
}
