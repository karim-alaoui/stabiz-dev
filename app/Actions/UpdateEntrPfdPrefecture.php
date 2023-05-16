<?php


namespace App\Actions;


use App\Models\EntrepreneurProfile;
use App\Models\Prefecture;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateEntrPfdPrefecture
 * @package App\Actions
 */
class UpdateEntrPfdPrefecture
{
    /**
     * @param EntrepreneurProfile $profile
     * @param array $data
     * @throws Exception
     */
    public static function execute(EntrepreneurProfile $profile, array $data)
    {
        ExcOnUserTypeMismatch::execute($profile->user, User::ENTR);
        $data = Prefecture::query()
            ->whereIn('id', $data)
            ->get()
            ->pluck('id')
            ->toArray();
        
        $data = array_map(fn($val) => ['prefecture_id' => $val], array_unique($data));
        if (count($data)) {
            DB::transaction(function () use ($profile, $data) {
                $profile->pfdPrefectures()->delete();
                $profile->pfdPrefectures()->createMany($data);
            });
        }
    }
}
