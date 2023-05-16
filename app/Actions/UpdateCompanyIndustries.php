<?php


namespace App\Actions;


use App\Models\FdrCompanyIndustry;
use App\Models\FounderProfile;
use Illuminate\Support\Facades\DB;

/**
 * Update company industries for founder's companies
 * Class UpdateCompanyIndustries
 * @package App\Actions
 */
class UpdateCompanyIndustries
{
    /**
     * @param FounderProfile $founderProfile
     * @param array $data
     * @return void
     */
    public static function execute(FounderProfile $founderProfile, array $data): void
    {
        $data = array_unique($data);
        $data = array_map(function ($value) use ($founderProfile) {
            return new FdrCompanyIndustry(['industry_id' => $value]);
        }, $data);

        DB::transaction(function () use ($founderProfile, $data) {
            if (count($data)) {
                $founderProfile->companyIndustries()->delete();
                $founderProfile->companyIndustries()->saveMany($data);
            }
        });
    }
}
