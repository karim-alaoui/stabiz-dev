<?php

namespace App\Http\Requests;

use App\Actions\PaginationRules;
use App\Models\Area;
use App\Models\EducationBackground;
use App\Models\IncomeRange;
use App\Models\Industry;
use App\Models\LangLevel;
use App\Models\Occupation;
use App\Models\Prefecture;
use App\Models\PresentPost;
use App\Models\WorkingStatus;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @queryParam school_name No-example
 * @queryParam working_status_id No-example
 * @queryParam present_company No-example
 * @queryParam present_post_id No-example
 * @queryParam industry_id comma separated string like `1,2,3` No-example
 * @queryParam occupation_id No-example
 * @queryParam eng_lang_level lang level id No-example
 * @queryParam lang_ability lang level id No-example
 * @queryParam management_exp boolean No-example
 * @queryParam education_background_id No-example
 * @queryParam annual_income income range id No-example
 * @queryParam page current page number for pagination Example: 2
 * @queryParam per_page the number of results to return per request. Default value is 15 Example: 15
 */
class SearchEntrByFdrReq extends FormRequest
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
        $validate = fn($table) => ['nullable', 'integer', "exists:$table,id"];

        return array_merge(PaginationRules::execute(), [
            // validate fields that depends on database value
            'working_status_id' => $validate((new WorkingStatus())->getTable()),
            'present_post_id' => $validate((new PresentPost())->getTable()),
            'industry_id' => $validate((new Industry())->getTable()),
            'occupation_id' => $validate((new Occupation())->getTable()),
            'eng_lang_level' => $validate((new LangLevel())->getTable()),
            'lang_ability' => $validate((new LangLevel())->getTable()),
            'education_background_id' => $validate((new EducationBackground())->getTable()),
            'annual_income' => $validate((new IncomeRange())->getTable()),
            'prefecture_id' => $validate((new Prefecture())->getTable()),
            'area_id' => $validate((new Area())->getTable()),
        ]);
    }
}
