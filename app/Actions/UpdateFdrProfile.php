<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Models\FounderProfile;
use App\Models\User;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Update founder profile
 * only for users whose type are founder
 * Class UpdateFdrProfile
 * @package App\Actions
 */
class UpdateFdrProfile
{
    /**
     * @param User $user
     * @param array $data
     * @throws ActionException
     * @throws Exception
     */
    public static function execute(User $user, array $data)
    {
        DB::beginTransaction();
        if ($user->type != User::FOUNDER) throw new ActionException(__('User type has to be founder'));

        $fdrProfile = $user->fdrProfile;
        if (!$fdrProfile) $fdrProfile = new FounderProfile();

        $update = Arr::only($data, [
            'company_name',
            'area_id',
            'prefecture_id',
            'is_listed_company',
            'no_of_employees',
            'capital',
            'last_year_sales',
            'established_on',
            'business_partner_company',
            'major_bank',
            'company_features',
            'job_description',
            'application_conditions',
            'employee_benefits',
            'offered_income_range_id',
            'work_start_date_4_entr'
        ]);

        $booleanFields = [
            'is_listed_company'
        ];

        foreach ($update as $column => $value) {
            if (Arr::exists($data, $column)) {
                $fdrProfile->{$column} = in_array($column, $booleanFields) ? db_bool_val($value) : $value;
            }
        }

        $fdrProfile->save();

        UpdateCompanyIndustries::execute($fdrProfile, Arr::get($data, 'company_industry_ids', []));
        UpdateAfflCom::execute($fdrProfile, Arr::get($data, 'affiliated_companies', []));
        UpdateStockHolders::execute($fdrProfile, Arr::get($data, 'major_stock_holders', []));
        UpdateFdrPfdIndustries::execute(
            $fdrProfile,
            Arr::get($data, 'pfd_industry_ids', [])
        );
        UpdateFdrPfdPrefecture::execute(
            $fdrProfile,
            Arr::get($data, 'pfd_prefecture_ids', [])
        );
        UpdatePfdPositions::execute($fdrProfile, Arr::get($data, 'pfd_position_ids', []));

        DB::commit();
    }
}
