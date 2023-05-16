<?php


namespace App\Actions;


use App\Models\FounderProfile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateFdrPfdPrefecture
 * @package App\Actions
 */
class UpdateFdrPfdPrefecture
{
    /**
     * @param FounderProfile $fdrProfile
     * @param array $data
     * @throws Exception
     */
    public static function execute(FounderProfile $fdrProfile, array $data)
    {
        //ExcOnUserTypeMismatch::execute($fdrProfile->user, User::FOUNDER);
        $data = array_map(fn($value) => ['prefecture_id' => $value], array_unique($data));
        if (count($data)) {
            DB::transaction(function () use ($fdrProfile, $data) {
                $fdrProfile->pfdPrefectures()->delete();
                $fdrProfile->pfdPrefectures()->createMany($data);
            });
        }
    }
}
