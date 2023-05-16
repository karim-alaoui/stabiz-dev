<?php

namespace App\Http\Requests;

use App\Actions\MakeEditRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateTopicReq
 * @package App\Http\Requests
 */
class UpdateTopicReq extends FormRequest
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
        $rules = (new CreateNewsTopicReq())->rules();
        return MakeEditRules::execute($rules);
    }
}
