<?php

namespace App\Http\Requests;

use App\Models\EmailTemplate;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam comment string Add comment on the email template like when it's send, what condition is there to be sent etc Upto the user No-example
 */
class CreateTemplateReq extends FormRequest
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
        return [
            'subject' => ['required', 'string', 'max:255', 'unique:' . (new EmailTemplate())->getTable() . ',subject'],
            'body' => ['required', 'string', 'max:65000'],
            'comment' => ['nullable', 'string', 'max:255'],
        ];
    }
}
