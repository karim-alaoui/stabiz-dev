<?php


namespace App\Actions;


use App\Models\FounderProfile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Update preferred industries of the founder
 * Basically, the industries from which the founder
 * would like to have entrepreneurs
 * Class UpdateFdrPfdIndustries
 * @package App\Actions
 */
class UpdateFdrPfdIndustries
{
    /**
     * @param FounderProfile $fdrProfile
     * @param array $data
     * @throws Exception
     */
    public static function execute(FounderProfile $fdrProfile, array $data): void
    {
        //ExcOnUserTypeMismatch::execute($fdrProfile->user, User::FOUNDER);
        $data = array_map(fn($value) => ['industry_id' => $value], array_unique($data));
        if (count($data)) {
            DB::transaction(function () use ($fdrProfile, $data) {
                $fdrProfile->pfdIndustries()->delete();
                $fdrProfile->pfdIndustries()->createMany($data);
            });
        }
    }
}
