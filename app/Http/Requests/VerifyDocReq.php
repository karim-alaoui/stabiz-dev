<?php

namespace App\Http\Requests;

use App\Models\UploadedDoc;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @bodyParam remarks string you can add remarks while verifying a document. Specially, useful when rejected something. No-example
 */
class VerifyDocReq extends FormRequest
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
        return [
            'id' => ['required', 'integer', 'exists:' . (new UploadedDoc())->getTable() . ',id'],
            'state' => ['required', 'string', Rule::in(['approved', 'rejected'])],
            'remarks' => ['nullable', 'string', 'max:500']
        ];
    }
}
