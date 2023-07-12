<?php


namespace App\Actions;

use Carbon\Carbon;
use App\Models\User;
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
     */
    public static function execute(array $query)
    {
        /**@var Builder $users */
        $users = User::query()->entrepreneurs();

        $users = $users->whereHas('entrProfile', function (Builder $q) use ($query) {
            $where = [];

            //area filter
            $area = Arr::get($query, 'area_id');
            if ($area) $where[] = ['area_id', $area];

            //prefecture filter
            $prefecture = Arr::get($query, 'prefecture_id');
            if ($prefecture) $where[] = ['prefecture_id', $prefecture];

            //education background filter
            $educationBackground = Arr::get($query, 'education_background_id');
            if ($educationBackground) $where[] = ['education_background_id', $educationBackground];

            //school name filter
            $schoolName = Arr::get($query, 'school_name');
            if ($schoolName) $where[] = ['school_name', 'ilike', "%$schoolName%"];

            //school major filter
            $schoolMajor = Arr::get($query, 'school_major');
            if ($schoolMajor) $where[] = ['school_major', 'ilike', "%$schoolMajor%"];

            //english level filter
            $engLevel = Arr::get($query, 'en_lang_level_id');
            if ($engLevel) $where[] = ['en_lang_level_id', $engLevel];

            //management experience filter
            $mgmtExp = Arr::get($query, 'management_exp_id');
            if ($mgmtExp) $where[] = ['management_exp_id', $mgmtExp];

            //position filter
            $presentPost = Arr::get($query, 'present_post_id');
            if ($presentPost) $where[] = ['present_post_id', $presentPost];
            
            //working status filter
            $workingStatus = Arr::get($query, 'working_status_id');
            if ($workingStatus) $where[] = ['working_status_id', $workingStatus];

            //Expected income range filter
            $expectedIncome = Arr::get($query, 'expected_income_range_id');
            if ($expectedIncome) $where[] = ['expected_income_range_id', $expectedIncome];

            $q->where($where);
        });

        //Age filter
        $minAge = Arr::get($query, 'min_age');
        $maxAge = Arr::get($query, 'max_age');
        if ($minAge) {
            $currentDate = Carbon::now();
            $users = $users->where('dob', '<=', $currentDate->subYears($minAge)->format('Y-m-d'));
        }
        if ($maxAge) {
            $currentDate = Carbon::now();
            $users = $users->where('dob', '>=', $currentDate->subYears($maxAge)->format('Y-m-d'));
        }

        //Gender filter
        $gender = Arr::get($query, 'gender');
        if ($gender) $users = $users->where('gender', $gender);

        //--------------------------------------

        //industry exp filter
        $industry = Arr::get($query, 'industry');
        if ($industry) {
            $users = $users->whereHas('entrProfile.expIndustries', function (Builder $q) use ($industry) {
                $q->where('industry_id', $industry);
            });
        }

        //pref industry filter
        $prefIndustry = Arr::get($query, 'pref_industry');
        if ($prefIndustry) {
            $users = $users->whereHas('entrProfile.pfdIndustries', function (Builder $q) use ($prefIndustry) {
                $q->where('industry_id', $prefIndustry);
            });
        }

        //pref position filter
        $prefPosition = Arr::get($query, 'pref_position');
        if ($prefPosition) {
            $users = $users->whereHas('entrProfile.pfdPositions', function (Builder $q) use ($prefPosition) {
                $q->where('position_id', $prefPosition);
            });
        }

        //pref occupation filter
        $prefOccupation = Arr::get($query, 'pref_occupation');
        if ($prefOccupation) {
            $users = $users->whereHas('entrProfile.pfdOccupations', function (Builder $q) use ($prefOccupation) {
                $q->where('occupation_id', $prefOccupation);
            });
        }

        //pref area filter
        $prefArea = Arr::get($query, 'pref_area');
        if ($prefArea) {
            $users = $users->whereHas('entrProfile.pfdAreas', function (Builder $q) use ($prefArea) {
                $q->where('area_id', $prefArea);
            });
        }

        return $users->pluck('id')->all();
    }
}
