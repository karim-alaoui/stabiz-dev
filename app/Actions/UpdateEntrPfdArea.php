<?php 


namespace App\Actions;


use App\Models\EntrepreneurProfile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Preferred areas where the entrepreneur would like to work in
 * Class UpdateEntrPfdArea
 * @package App\Actions
 */
class UpdateEntrPfdArea
{
    /**
     * @param EntrepreneurProfile $profile
     * @param array $data
     * @throws Exception
     */
    public static function execute(EntrepreneurProfile $profile, array $data)
    {
        ExcOnUserTypeMismatch::execute($profile->user, User::ENTR);
        $data = array_map(fn($val) => ['area_id' => $val], array_unique($data));
        if (count($data)) {
            DB::transaction(function () use ($data, $profile) {
                $profile->pfdAreas()->delete();
                $profile->pfdAreas()->createMany($data);
            });
        }
    }
}
