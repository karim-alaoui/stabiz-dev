<?php

namespace App\Actions;

use App\Exceptions\ActionException;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Get the recommendation list of founder
 *
 * The staff would manually recommend entrepreneur to founder from this list
 */
class RecUserSearch
{
    /**
     * Build an array which is used on where query for user table
     * User table is common for both entrepreneurs and founders
     * @param User $user
     * @param array $query
     * @return array
     * @throws ActionException
     */
    private static function buildUserTableWhereArray(User $user, array $query): array
    {
        $where = [];
        if (strtolower($user->type) == strtolower(User::ENTR)) {
            $where[] = ['type', 'ilike', User::FOUNDER];
        } else {
            $where[] = ['type', 'ilike', User::ENTR];
        }

        $ageGreaterThan = Arr::get($query, 'age_greater_than');
        if ($ageGreaterThan) {
            $where[] = [DB::raw("date_part('year', age(now(), dob))"), '>=', $ageGreaterThan];
        }

        $ageLowerThan = Arr::get($query, 'age_lower_than');
        if ($ageLowerThan) {
            $where[] = [DB::raw("date_part('year', age(now(), dob))"), '<=', $ageLowerThan];
        }

        if ($ageGreaterThan && $ageLowerThan && $ageLowerThan < $ageGreaterThan) {
            $msg = __('Age lower than has to be greater than age greater than');
            throw new ActionException($msg);
        }

        $gender = Arr::get($query, 'gender');
        if ($gender) $where[] = ['gender', 'ilike', $gender];

        return $where;
    }

    /**
     * Search on entrepreneur profile of the user
     * @param Builder $userQuery
     * @param array $query
     * @return Builder
     */
    private static function searchOnEntrProfile(Builder $userQuery, array $query): Builder
    {
        $school = Arr::get($query, 'school_name');
        if ($school) {
            $userQuery = $userQuery->whereHas('entrProfile', fn(Builder $q) => $q->where('school_name', 'ilike', "%$school%"));
        }

        $schoolMajor = Arr::get($query, 'school_major');
        if ($schoolMajor) {
            $userQuery = $userQuery->whereHas('entrProfile', fn(Builder $q) => $q->where('school_major', 'ilike', "%$schoolMajor%"));
        }

        $presentCom = Arr::get($query, 'present_company');
        if ($presentCom) {
            $userQuery = $userQuery->whereHas('entrProfile', fn(Builder $q) => $q->where('present_company', 'ilike', "%$presentCom%"));
        }

        $workingStatus = Arr::get($query, 'working_status_id');
        if ($workingStatus) {
            $userQuery = $userQuery->whereHas('entrProfile', function (Builder $q) use ($workingStatus) {
                $q->where('working_status_id', $workingStatus);
            });
        }

        $presentPost = Arr::get($query, 'present_post_id');
        if ($presentPost) {
            $userQuery = $userQuery->whereHas('entrProfile', fn(Builder $q) => $q->where('present_post_id', $presentPost));
        }

        $occupation = Arr::get($query, 'occupation_id');
        if ($occupation) {
            $userQuery = $userQuery->whereHas('entrProfile', fn(Builder $q) => $q->where('occupation_id', $occupation));
        }

        $transfer = Arr::get($query, 'transfer');
        if ($transfer) {
            $userQuery = $userQuery->whereHas('entrProfile', fn(Builder $q) => $q->where('transfer', 'ilike', $transfer));
        }

        $mgmtExp = Arr::get($query, 'management_exp_id');
        if ($mgmtExp) {
            $userQuery = $userQuery->whereHas('entrProfile', fn(Builder $q) => $q->where('management_exp_id', $mgmtExp));
        }

        $pfdWorkingIndus = Arr::get($query, 'pfd_industry_ids');
        if ($pfdWorkingIndus) {
            $pfdWorkingIndus = explode(',', $pfdWorkingIndus);
            $userQuery = $userQuery->whereHas('entrProfile.pfdIndustries', function (Builder $q) use ($pfdWorkingIndus) {
                $q->whereIn('industry_id', $pfdWorkingIndus);
            });
        }

        $pfdPositions = Arr::get($query, 'pfd_position_ids');
        if ($pfdPositions) {
            $pfdPositions = explode(',', $pfdPositions);
            $userQuery = $userQuery->whereHas('entrProfile.pfdPositions', function (Builder $q) use ($pfdPositions) {
                $q->whereIn('position_id', $pfdPositions);
            });
        }

        $pfdPrefectures = Arr::get($query, 'pfd_prefecture_ids');
        if ($pfdPrefectures) {
            $pfdPrefectures = explode(',', $pfdPrefectures);
            $userQuery = $userQuery->whereHas('entrProfile.pfdPrefectures', function (Builder $q) use ($pfdPrefectures) {
                $q->whereIn('prefecture_id', $pfdPrefectures);
            });
        }

        $expectedIncome = Arr::get($query, 'expected_income_range_id');
        if ($expectedIncome) {
            $userQuery = $userQuery->whereHas('entrProfile', fn(Builder $q) => $q->where('expected_income_range_id', $expectedIncome));
        }

        return $userQuery;
    }

    /**
     * @param User $user - the user whose recommendation list you want to get
     * @param array $query
     * @return LengthAwarePaginator
     * @throws ActionException
     */
    public static function execute(User $user, array $query = []): LengthAwarePaginator
    {
        $userType = strtolower($user->type);
        $supported = [strtolower(User::FOUNDER), strtolower(User::ENTR)];

        if (!in_array($userType, $supported)) {
            throw new ActionException(__('Invalid user type'));
        }

        $where = self::buildUserTableWhereArray($user, $query);

        // this query will be used as a subquery to exclude the users which have already been recommended
        $recommendedUsers = Recommendation::query()
            ->select('recommended_user_id')
            ->where('recommended_to_user_id', $user->id);
        $recommendedUsersSql = $recommendedUsers->toSql();

        $userQuery = User::query();

        $name = Arr::get($query, 'name');
        if ($name) {
            $userQuery = $userQuery->where(function (Builder $q) use ($name) {
                $q->where('first_name', 'ilike', "%$name%")
                    ->orWhere('last_name', 'ilike', "%$name%")
                    ->orWhere('first_name_cana', 'ilike', "%$name%")
                    ->orWhere('last_name_cana', 'ilike', "%$name%");
            });
        }

        if ($userType == strtolower(User::FOUNDER)) {
            $userQuery = self::searchOnEntrProfile($userQuery, $query);
        }

        return $userQuery
            ->where($where)
            // exclude the users which have already been recommended
            ->whereRaw("id not in ($recommendedUsersSql)", $recommendedUsers->getBindings())
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }
}
