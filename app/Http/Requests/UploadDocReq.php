<?php

namespace App\Http\Requests;

use App\Models\UploadedDoc;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @bodyParam doc_name string required either of `all_historical_matter_cert`,`fin_stmt_prev_fy`,`tax_pymt_prev_period` No-example
 */
class UploadDocReq extends FormRequest
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
            'file' => ['required', 'file', 'max:5000', 'mimes:jpg,png,pdf'],
            'doc_name' => [
                'required',
                'string',
                Rule::in(UploadedDoc::DOC_NAMES),
            ]
        ];
    }
}
