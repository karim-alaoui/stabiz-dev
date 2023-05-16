<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Storage;

/**
 * Class FounderProfileResource
 * @package App\Http\Resources
 */
class FounderProfileResource extends JsonResource
{
    /**
     * This will pluck the inner relation and will return it as a collection
     * So, if it was before
     * [{id: '', relation_name => relation_value_1}, {id: '', relation_name => relation_value_2},]
     * it would become now
     * [relation_value1, relation_value2]
     * @param $relationName
     * @param $nestedRelationName
     * @return mixed
     */
    private function pluckRelation($relationName, $nestedRelationName): mixed
    {
        $relation = $this->whenLoaded($relationName);
        return $this->when(!empty((array)$relation), function () use ($relation, $nestedRelationName) {
            if ($relation instanceof MissingValue) return [];
            $relationLoaded = count($relation) && $relation[0]->relationLoaded($nestedRelationName);
            return $relationLoaded ? $relation->pluck($nestedRelationName) : [];
        });
    }

    /**
     * Pluck key from relation and make it into an array
     * Example - it was like this
     * [{id: id_here, name: name1}, {id: id, name: name2}..]
     * it would become
     * [name1, name2] if we pluck the name key
     * @param $relationName
     * @param $keyName
     * @return mixed
     */
    private function pluckKey($relationName, $keyName): mixed
    {
        $relation = $this->whenLoaded($relationName);
        return $this->when(!empty((array)$relation), function () use ($relation, $keyName) {
            if ($relation instanceof MissingValue) return [];
            return $relation->pluck($keyName);
        });
    }

    /**
     * Transform the resource into an array.
     *
     */
    public function toArray($request): array
    {
        $companyLogo = null;
        $companyLogoDisk = $this->company_logo_disk;
        $comLogoPath = $this->company_logo_path;
        if ($comLogoPath && $companyLogoDisk) {
            $companyLogo = Storage::disk($companyLogoDisk)->url($comLogoPath);
        }

        $comBanner = null;
        $comBanDisk = $this->company_banner_disk;
        $comBanPath = $this->company_banner_img_path;
        if ($comBanDisk && $comBanPath) {
            $comBanner = Storage::disk($comBanDisk)->url($comBanPath);
        }

        return [
            'company_name' => $this->company_name,
            // industries where the founder's company belong from
            'company_industries' => IndustryResource::collection($this->pluckRelation('companyIndustries', 'industry')),
            'is_listed_company' => $this->is_listed_company,
            'area' => new AreaResource($this->whenLoaded('area')),
            'prefecture' => new PrefectureResource($this->whenLoaded('prefecture')),
            'pfd_prefectures' => PrefectureResource::collection(
                $this->pluckRelation('pfdPrefectures', 'prefecture')
            ),
            'affiliated_companies' => $this->pluckKey('affiliatedCompanies', 'company_name'),
            'major_stock_holders' => $this->pluckKey('majorStockHolders', 'name'),
            // preferred industries from which the founder would like entrepreneurs
            'pfd_industries' => IndustryResource::collection($this->pluckRelation('pfdIndustries', 'industry')),
            // preferred positions for which the founder would like entrepreneurs
            'pfd_positions' => PositionResource::collection($this->pluckRelation('pfdPositions', 'position')),
            'no_of_employees' => $this->no_of_employees,
            'capital' => $this->capital,
            'last_year_sales' => $this->last_year_sales,
            'established_on' => $this->established_on,
            'business_partner_company' => $this->business_partner_company,
            'major_bank' => $this->major_bank,
            'company_features' => $this->company_features,
            'job_description' => $this->job_description,
            'application_conditions' => $this->application_conditions,
            'employee_benefits' => $this->employee_benefits,
            'offered_income' => new IncomeRangeResource($this->whenLoaded('offeredIncome')),
            'work_start_date_4_entr' => $this->work_start_date_4_entr,
            'company_logo' => $companyLogo,
            'company_banner' => $comBanner
        ];
    }
}
