<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Exceptions\ActionValidationException;
use App\Models\EducationBackground;
use App\Models\EntrepreneurProfile;
use App\Models\LangLevel;
use App\Models\Language;
use App\Models\MgmtExp;
use App\Models\Occupation;
use App\Models\PresentPost;
use App\Models\User;
use App\Models\WorkingStatus;
use App\Rules\ValidDate;
use App\Traits\RelationshipTrait;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Update entrepreneur profile of the user if the user is entrepreneur
 * Each user can have either of two types - founder and entrepreneur
 * Class UpdateEntrProfile
 * @package App\Actions
 */
class UpdateEntrProfile
{
    use RelationshipTrait;

    /**
     * Validate data provided in the $data array
     * @param array $data
     * @throws ActionValidationException
     */
    private static function validate(array $data)
    {
        $otherPostId = PresentPost::where('name', 'other')->first();
        if ($otherPostId) $otherPostId = $otherPostId->id;
        $otherLangId = Language::where('name', 'other')->first()?->id;

        $validator = Validator::make($data, [
            'education_background_id' => ['exists:' . (new EducationBackground())->getTable() . ',id'],
            'working_status_id' => ['exists:' . (new WorkingStatus())->getTable() . ',id'],
            'present_post_id' => ['exists:' . (new PresentPost())->getTable() . ',id'],
            'occupation_id' => ['exists:' . (new Occupation())->getTable() . ',id'],
            'lang_id' => ['exists:languages,id'],
            'lang_other' => [Rule::requiredIf(Arr::get($data, 'lang_id') == $otherLangId && $otherLangId)],
            'lang_level_id' => ['exists:' . (new LangLevel())->getTable() . ',id'],
            'en_lang_level_id' => ['exists:' . (new LangLevel())->getTable() . ',id'],
            'transfer' => [Rule::in(['yes', 'no', 'only domestic', 'only overseas'])],
            // if present post id is other then must provide present post other field which is text field
            'present_post_other' => [Rule::requiredIf($otherPostId && Arr::get($data, 'present_post_id') == $otherPostId)],
            'work_start_date' => ['nullable', new ValidDate()],
            'school_major' => ['nullable', 'string', 'max:255'],
            'management_exp_id' => ['nullable', 'integer', 'exists:' . (new MgmtExp())->getTable() . ',id']
        ]);

        if ($validator->fails()) {
            throw new ActionValidationException($validator);
        }
    }

    /**
     * @param User|Authenticatable $user
     * @param array $data - data that has to be updated
     * @return mixed
     * @throws ActionException
     * @throws ActionValidationException
     * @throws Exception
     */
    public static function execute(User|Authenticatable $user, array $data): mixed
    {
        DB::beginTransaction();
        if ($user->type != 'entrepreneur') {
            throw new ActionException(__('exception.not_entr'));
        }

        self::validate($data);

        $profile = $user->entrProfile;
        if (!$profile) {
            $profile = new EntrepreneurProfile();
            $profile->user_id = $user->id;
        }
        $update = [
            'address',
            'education_background_id',
            'school_name',
            'working_status_id',
            'present_company',
            'present_post_id',
            'occupation_id',
            'lang_id',
            'lang_level_id',
            'en_lang_level_id',
            'transfer',
            'expected_income_range_id',
            'prefecture_id',
            'area_id',
            'work_start_date',
            'school_major',
            'management_exp_id'
        ];

        foreach ($update as $column) {
            if (Arr::exists($data, $column)) $profile->{$column} = Arr::get($data, $column);
        }

        $presentPostId = Arr::get($data, 'present_post_id');
        if ($presentPostId) {
            $isOtherPost = PresentPost::other()->where('id', $presentPostId)->first();
            $profile->present_post_other = $isOtherPost ? Arr::get($data, 'present_post_other') : null;
        }

        $langId = Arr::get($data, 'lang_id');
        if ($langId) {
            $isOtherLang = Language::other()->where('id', $langId)->first();
            $profile->lang_other = $isOtherLang ? Arr::get($data, 'lang_other') : null;
        }

        $profile->save();
        
        $pfdIndustries = Arr::get($data, 'pfd_industry_ids', []);
        if (count($pfdIndustries)) UpdateEntrPfdIndustry::execute($profile, $pfdIndustries);

        $industryIds = Arr::get($data, 'industry_ids', []);
        if (count($industryIds)) UpdateEntrIndustry::execute($profile, $industryIds);

        $pfdPrefectures = Arr::get($data, 'pfd_prefecture_ids', []);
        if (count($pfdPrefectures)) UpdateEntrPfdPrefecture::execute($profile, $pfdPrefectures);

        $pfdPositions = Arr::get($data, 'pfd_position_ids', []);
        if (count($pfdPositions) && gettype($pfdPositions) == 'array') UpdateEntrPfdPositions::execute($profile, $pfdPositions);
        
        $pfdOccupations = Arr::get($data, 'pfd_occupation_ids', []);
        if (count($pfdOccupations)) UpdateEntrPfdOccupation::execute($profile, $pfdOccupations);
        
        $pfdAreas = Arr::get($data, 'pfd_area_ids', []);
        if (count($pfdAreas)) UpdateEntrPfdArea::execute($profile, $pfdAreas);
        
        DB::commit();
        return $profile;
    }
}
