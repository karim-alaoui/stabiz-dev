<?php


namespace App\Actions;


use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * Search entrepreneurs
 * Class SearchEntrs
 * @package App\Actions
 */
class SearchEntrs
{
    /**
     * @param array $query
     * @return LengthAwarePaginator
     */
    public static function execute(array $query): LengthAwarePaginator
    {
        /**@var Builder $users */
        $users = User::query()->entrepreneurs();

        $users = $users->whereHas('entrProfile', function (Builder $q) use ($query) {
            $where = [];

            $school = Arr::get($query, 'school_name');
            if ($school) $where[] = ['school_name', 'ilike', "%$school%"];

            $workingStatus = Arr::get($query, 'working_status_id');
            if ($workingStatus) $where[] = ['working_status_id', $workingStatus];

            $presentPost = Arr::get($query, 'present_post_id');
            if ($presentPost) $where[] = ['present_post_id', $presentPost];

            $industry = Arr::get($query, 'industry_id');
//            if ($industry) $where[] = ['industry_id', $industry];

            $occupation = Arr::get($query, 'occupation_id');
            if ($occupation) $where[] = ['occupation_id', $occupation];

            $engLevel = Arr::get($query, 'eng_lang_level');
            if ($engLevel) $where[] = ['en_lang_level_id', $engLevel];

            $langLevel = Arr::get($query, 'lang_ability');
            if ($langLevel) $where[] = ['lang_level_id', $langLevel];

            $mgmtExp = Arr::exists($query, 'management_exp_id');
            if ($mgmtExp) $where[] = ['management_exp_id', $mgmtExp];

            $eduBg = Arr::get($query, 'education_background_id');
            if ($eduBg) $where[] = ['education_background_id', $eduBg];

            $q->where($where);
        });

        $income = Arr::get($query, 'annual_income');
        if ($income) $users = $users->where('income_range_id', $income);

        return $users->select(['id'])
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }
}
