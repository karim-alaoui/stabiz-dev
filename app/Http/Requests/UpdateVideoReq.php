<?php

namespace App\Http\Requests;

use App\Actions\MakeEditRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoReq extends FormRequest
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
        $creteRules = (new StoreVideoReq())->rules();
        return array_merge(MakeEditRules::execute($creteRules), [
            // add new rules if needed
        ]);
    }
}
