<?php

namespace App\Actions;

use App\Models\EntrepreneurProfile;
use App\Models\Position;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateEntrPfdPositions
{
    /**
     * @param EntrepreneurProfile $profile
     * @param array $data - array of position ids
     * @throws Exception
     */
    public static function execute(EntrepreneurProfile $profile, array $data)
    {
        ExcOnUserTypeMismatch::execute($profile->user, User::ENTR);

        $positionIds = Position::query()
            ->whereIn('id', $data)
            ->get()
            ->pluck('id')
            ->toArray();

        if (count($positionIds) == 0) return;

        $data = array_map(fn($value) => ['position_id' => $value], array_unique($positionIds));
        if (count($data)) {
            DB::transaction(function () use ($profile, $data) {
                $profile->pfdPositions()->delete();
                $profile->pfdPositions()
                    ->createMany($data);
            });
        }
    }
}
