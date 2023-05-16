<?php

namespace App\Http\Requests;

use App\Models\Area;
use App\Models\IncomeRange;
use App\Models\Industry;
use App\Models\Position;
use App\Models\Prefecture;
use App\Rules\ValidateDateTime;
use App\Rules\ValidateEmptyHTML;
use App\Rules\ValidDate;
use App\Rules\ValuesExist;
use App\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam dob string date of birth in this format `YYYY-MM-DD` No-example
 * @bodyParam company_industry_ids integer[] No-example
 * @bodyParam established_on string date format - `YYYY-MM-DD` the user may not provide the date if the user doesn't, send `01` for `date` No-example
 * @bodyParam pfd_position_ids [] position ids. Max 3 No-example
 * @bodyParam offered_income_range_id integer income range id No-example
 * @bodyParam work_start_date_4_entr string date in string format No-example
 */
class UpdateFdrReq extends FormRequest
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
     * @throws \Exception
     */
    public function rules(): array
    {
        return array_merge($this->commonUserValidationRules(), [
            'company_name' => ['sometimes', 'required', 'max:255'],
            'company_industry_ids' => ['sometimes', 'required', 'array', 'max:3', new ValuesExist('id', Industry::class)],
            'area_id' => ['sometimes', 'required', 'integer', 'exists:' . (new Area())->getTable() . ',id'],
            'prefecture_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:' . (new Prefecture())->getTable() . ',id',
            ],
            'is_listed_company' => ['sometimes', 'required', 'boolean'],
            'no_of_employees' => ['sometimes', 'required', 'integer'],
            'capital' => ['sometimes', 'required', 'integer'],
            'last_year_sales' => ['sometimes', 'required', 'integer'],
            'established_on' => ['sometimes', 'required', new ValidateDateTime()],
            'business_partner_company' => ['sometimes', 'required', 'string', 'max:255'],
            'major_bank' => ['sometimes', 'required', 'string', 'max:255'],
            'company_features' => ['sometimes', 'required', 'string', new ValidateEmptyHTML()],
            'job_description' => ['sometimes', 'required', 'string', new ValidateEmptyHTML()],
            'application_conditions' => ['sometimes', 'required', 'string', new ValidateEmptyHTML()],
            'employee_benefits' => ['sometimes', 'required', 'string', new ValidateEmptyHTML()],
            'affiliated_companies' => ['sometimes', 'required', 'array'],
            'major_stock_holders' => ['sometimes', 'required', 'array'],
            'pfd_industry_ids' => ['sometimes', 'required', 'array', 'max:3', new ValuesExist('id', Industry::class)],
            'pfd_prefecture_ids' => ['sometimes', 'required', 'array', 'max:3', new ValuesExist('id', Prefecture::class)],
            'pfd_position_ids' => ['sometimes', 'required', 'array', 'max:3', new ValuesExist('id', Position::class)],
            'offered_income_range_id' => ['sometimes', 'required', 'integer', 'exists:' . (new IncomeRange())->getTable() . ',id'],
            'work_start_date_4_entr' => ['sometimes', 'required', 'string', new ValidDate(), 'after:yesterday']
        ]);
    }

    public function messages(): array
    {
        return [
            'company_industry_ids.max' => __('You can select max 3 industries for your company'),
            'pf_industry_ids.max' => __('You can select max 3 industries for your preferred industries'),
            'pfd_prefecture_ids.max' => __('You can select max 3 for your preferred prefectures'),
            'pfd_position_ids.max' => __('You can select max 3 for your preferred positions'),
        ];
    }
}
