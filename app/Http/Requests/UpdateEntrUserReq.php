<?php /** @noinspection PhpMissingDocCommentInspection */

namespace App\Http\Requests;

use App\Models\Area;
use App\Models\EducationBackground;
use App\Models\IncomeRange;
use App\Models\Industry;
use App\Models\LangLevel;
use App\Models\Language;
use App\Models\MgmtExp;
use App\Models\Occupation;
use App\Models\Position;
use App\Models\Prefecture;
use App\Models\PresentPost;
use App\Models\WorkingStatus;
use App\Rules\ValidDate;
use App\Rules\ValuesExist;
use App\Traits\ValidationTrait;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @bodyParam dob date has to be a valid date in this format `YYYY-MM-DD` No-example
 * @bodyParam present_post_other string if user selected `other` from the dropdown of present post, then the user must fill in this category No-example
 * @bodyParam gender string either of `male`, `female` and `other` No-example
 * @bodyParam lang_id integer the lang id from `lang` in value section No-example
 * @bodyParam lang_other string from lang dropdown, when the user selects the value as other, the user will need to fill it No-example
 * @bodyParam lang_level_id integer the lang level id from `lang level` value endpoint No-example
 * @bodyParam en_lang_level_id integer the lang level id from `lang level` value endpoint No-example
 * @bodyParam expected_income_range_id integer income range id. This means expected income of the entrepreneur No-example
 * @bodyParam industry_ids [] array of industry id No-example
 * @bodyParam pfd_industry_ids [] array of industry ids No-example
 * @bodyParam pfd_prefecture_ids [] array of prefecture ids No-example
 * @bodyParam work_start_date string a date in string format No-example
 * @bodyParam pfd_position_ids [] array of position ids No-example
 */
class UpdateEntrUserReq extends FormRequest
{
    use ValidationTrait;

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
     * @throws Exception
     */
    public function rules(): array
    {
        $otherLangId = Language::where('name', 'other')->first()?->id;
        return array_merge($this->commonUserValidationRules(), [
            'address' => ['sometimes', 'required', 'string', 'max:400'],
            'education_background_id' => ['sometimes', 'required', 'integer', 'exists:' . (new EducationBackground())->getTable() . ',id'],
            'school_name' => ['sometimes', 'required', 'string', 'max:100'],
            'working_status_id' => ['sometimes', 'required', 'integer', 'exists:' . (new WorkingStatus())->getTable() . ',id'],
            'present_company' => ['sometimes', 'required', 'string', 'max:100'],
            'present_post_id' => ['sometimes', 'required', 'integer', 'exists:' . (new PresentPost())->getTable() . ',id'],
            // in present post, the user can select other,
            // if other is selected, then the user can put in this field
            'present_post_other' => [
                'nullable',
                'string',
                'max:100',
                Rule::requiredIf(function () {
                    $presentPostId = $this->present_post_id;
                    if ($presentPostId) {
                        // if selected other as present post, then this field is required
                        $isOtherPost = PresentPost::where('name', 'ilike', 'other')
                            ->where('id', $presentPostId)
                            ->first();

                        return (bool)$isOtherPost;
                    } else {
                        return false;
                    }
                }),
            ],
            'occupation_id' => ['sometimes', 'required', 'integer', 'exists:' . (new Occupation())->getTable() . ',id'],
            'lang_id' => ['sometimes', 'required', 'exists:languages,id'],
            // from lang_id, when the user select the value as other, the user will need to fill it
            'lang_other' => [
                'nullable',
                Rule::requiredIf($this->lang_id && $otherLangId && $this->lang_id == $otherLangId),
                // since english lang option is there separately, don't accept anything related to english
                Rule::notIn(['eng', 'english', 'en'])
            ],
            'lang_level_id' => ['sometimes', 'required', 'integer', 'exists:' . (new LangLevel())->getTable() . ',id'],
            'en_lang_level_id' => ['sometimes', 'required', 'integer', 'exists:' . (new LangLevel())->getTable() . ',id'],
            'transfer' => [
                'sometimes',
                'required',
                Rule::in([
                    'yes',
                    'no',
                    'only domestic',
                    'only overseas'
                ])
            ],
            'expected_income_range_id' => ['sometimes', 'required', 'integer', 'exists:' . (new IncomeRange())->getTable() . ',id'],
            'pfd_prefecture_ids' => ['sometimes', 'required', 'array', 'max:3', new ValuesExist('id', Prefecture::class)],
            'pfd_industry_ids' => ['sometimes', 'required', 'array', 'max:3', new ValuesExist('id', Industry::class)],
            'pfd_occupation_ids' => ['sometimes', 'required', 'array', 'max:3', new ValuesExist('id', Occupation::class)],
            'pfd_area_ids' => ['sometimes', 'required', 'array', 'max:3', 'exists:' . (new Area())->getTable() . ',id'],
            'industry_ids' => ['sometimes', 'required', 'array', 'max:3', new ValuesExist('id', Industry::class)],
            'prefecture_id' => ['sometimes', 'required', 'integer', 'exists:' . (new Prefecture())->getTable() . ',id'],
            'area_id' => ['sometimes', 'required', 'integer', 'exists:' . (new Area())->getTable() . ',id'],
            'work_start_date' => ['sometimes', 'required', 'string', new ValidDate(), 'after:yesterday'],
            'school_major' => ['sometimes', 'required', 'string'],
            'management_exp_id' => ['sometimes', 'required', 'integer', 'exists:' . (new MgmtExp())->getTable() . ',id'],
            'pfd_position_ids' => ['sometimes', 'required', 'array', new ValuesExist('id', Position::class), 'max:3']
        ]);
    }

    public function messages(): array
    {
        return [
            'present_post_other.required' => __('exception.fill_other_post'),
            'lang_other.required' => __('Please type other language since you selected other as language'),
            'work_start_date.after' => __('Work start date has to be after yesterday'),
            'pfd_position_ids.max' => __('You can provide 3 positions for preferred positions')
        ];
    }

    public function attributes(): array
    {
        return [
            'dob' => __('Age(dob)')
        ];
    }
}
