<?php


namespace App\Actions;


use App\Models\EntrepreneurProfile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Preferred occupations where the entrepreneur would like to work in
 * Class UpdateEntrPfdOccupation
 * @package App\Actions
 */
class UpdateEntrPfdOccupation
{
    /**
     * @param EntrepreneurProfile $profile
     * @param array $data
     * @throws Exception
     */
    public static function execute(EntrepreneurProfile $profile, array $data)
    {
        ExcOnUserTypeMismatch::execute($profile->user, User::ENTR);
        $data = array_map(fn($val) => ['occupation_id' => $val], array_unique($data));
        if (count($data)) {
            DB::transaction(function () use ($data, $profile) {
                $profile->pfdOccupations()->delete();
                $profile->pfdOccupations()->createMany($data);
            });
        }
    }
}
