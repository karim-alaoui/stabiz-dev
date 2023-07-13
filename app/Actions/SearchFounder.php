<?php


namespace App\Actions;


use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Models\FounderProfile;
use Carbon\Carbon;

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
     */
    public static function execute(array $query)
    {
        /** @var Builder $founderProfiles */
        $founderProfiles = FounderProfile::query();
        
        //capital filter
        $minCapital = Arr::get($query, 'min_capital');
        $maxCapital = Arr::get($query, 'max_capital');
        if ($minCapital !== null && is_numeric($minCapital)) {
            $founderProfiles->where('capital', '>=', $minCapital);
        }
        if ($maxCapital !== null && is_numeric($maxCapital)) {
            $founderProfiles->where('capital', '<=', $maxCapital);
        } 

        //area filter
        $areaId = Arr::get($query, 'area_id');
        if ($areaId) {
            $founderProfiles->where('area_id', $areaId);
        }

        //prefecture filter
        $prefectureId = Arr::get($query, 'prefecture_id');
        if ($prefectureId) {
            $founderProfiles->where('prefecture_id', $prefectureId);
        }

        //established on filter
        $minEstablishedOn = Arr::get($query, 'min_established_on');
        $maxEstablishedOn = Arr::get($query, 'max_established_on');
        if ($minEstablishedOn) {
            $minEstablishedOn = Carbon::parse($minEstablishedOn)->format('Y-m-d');
            $founderProfiles->where('established_on', '>=', $minEstablishedOn);
        }
        if ($maxEstablishedOn) {
            $maxEstablishedOn = Carbon::parse($maxEstablishedOn)->format('Y-m-d');
            $founderProfiles->where('established_on', '<=', $maxEstablishedOn);
        }

        //industry filter
        $industryIds = Arr::get($query, 'industry');
        if ($industryIds) {
            $industryIds = explode(',', $industryIds);
            $founderProfiles->whereHas('companyIndustries', function (Builder $q) use ($industryIds) {
                $q->whereIn('industry_id', $industryIds);
            });
        }

        //preferred Industries filter
        $preferredIndustries = Arr::get($query, 'pref_industries');
        if ($preferredIndustries) {
            $preferredIndustries = explode(',', $preferredIndustries);
            $founderProfiles->whereHas('pfdIndustries', function (Builder $q) use ($preferredIndustries) {
                $q->whereIn('industry_id', $preferredIndustries);
            });
        }

        //preferred Positions filter
        $preferredPositions = Arr::get($query, 'pref_positions');
        if ($preferredPositions) {
            $preferredPositions = explode(',', $preferredPositions);
            $founderProfiles->whereHas('pfdPositions', function (Builder $q) use ($preferredPositions) {
                $q->whereIn('position_id', $preferredPositions);
            });
        }

        //offered income range filter
        $expectingIncomeId = Arr::get($query, 'offered_income_range');
        if ($expectingIncomeId) {
            $founderProfiles->where('offered_income_range_id', $expectingIncomeId);
        }

        //listing division
        $IsListed = Arr::get($query, 'is_listed');
        if ($IsListed) {
            $founderProfiles->where('is_listed_company', $IsListed);
        }

        return $founderProfiles->pluck('id')->all();
    }
}
