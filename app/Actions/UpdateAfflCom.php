<?php


namespace App\Actions;


use App\Models\FounderProfile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Update affiliated companies of the founder
 * Class UpdateAfflCom
 * @package App\Actions
 */
class UpdateAfflCom
{
    /**
     * @param FounderProfile $fdrProfile
     * @param array $data
     * @throws Exception
     */
    public static function execute(FounderProfile $fdrProfile, array $data)
    {
        //ExcOnUserTypeMismatch::execute($fdrProfile->user, User::FOUNDER);
        $data = array_map(fn($value) => ['company_name' => $value], array_unique($data));
        if (count($data)) {
            DB::transaction(function () use ($data, $fdrProfile) {
                $fdrProfile->affiliatedCompanies()->delete();
                $fdrProfile->affiliatedCompanies()->createMany($data);
            });
        }
    }
}
