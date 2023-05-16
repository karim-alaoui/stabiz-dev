<?php


namespace App\Actions;


use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * Class SearchFounder
 * @package App\Actions
 */
class SearchFounder
{
    private static function searchIndustry(Builder $builder, array $industries): Builder
    {
        if (count($industries) == 0) return $builder;
        return $builder->where(function (Builder $whereQ) use ($industries) {
            // these industries will be pfd industries of the founder for which he would like entr
            $whereQ->whereHas('fdrProfile.pfdIndustries', function (Builder $q) use ($industries) {
                $q->whereIn('industry_id', $industries);
            }) // also search the company industries of the founder
            ->orWhereHas('fdrProfile.companyIndustries', function (Builder $q) use ($industries) {
                $q->whereIn('industry_id', $industries);
            });
        });
    }

    /**
     * @param array $query
     * @return LengthAwarePaginator
     */
    public static function execute(array $query): LengthAwarePaginator
    {
        /**@var Builder $users */
        $users = User::query()->founders()->select('id');

        $expectingIncomeId = Arr::get($query, 'expecting_income'); // this is income range id
        if ($expectingIncomeId) {
            $users = $users->whereHas('fdrProfile', function (Builder $q) use ($expectingIncomeId) {
                $q->where('offered_income_range_id', $expectingIncomeId);
            });
        }

        $industries = Arr::get($query, 'industries'); // industries which entr is looking for.
        if ($industries) {
            $industries = explode(',', $industries); // it's comma seperated string
            $users = self::searchIndustry($users, $industries);
        }

        $area = Arr::get($query, 'area_id');
        $prefecture = Arr::get($query, 'prefecture_id');
        if ($area || $prefecture) {
            $users = $users->whereHas('fdrProfile.pfdPrefectures.prefecture', function (Builder $q) use ($area, $prefecture) {
                if ($prefecture) $q->where('id', $prefecture);
                elseif ($area) $q->where('area_id', $area);
            });
        }

        $positions = Arr::get($query, 'positions');
        if ($positions) {
            $positions = explode(',', $positions); // comma separated string
            $users = $users->whereHas('fdrProfile.pfdPositions', function (Builder $q) use ($positions) {
                $q->whereIn('position_id', $positions);
            });
        }

        return $users
            ->with(['fdrProfile:user_id,company_name'])
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }
}
