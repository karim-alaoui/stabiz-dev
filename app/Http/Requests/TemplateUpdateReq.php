<?php

namespace App\Http\Requests;

use App\Actions\MakeEditRules;
use Illuminate\Foundation\Http\FormRequest;

class TemplateUpdateReq extends FormRequest
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
        $createRules = (new CreateTemplateReq())->rules();
        return MakeEditRules::execute($createRules);
    }
}
