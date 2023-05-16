<?php


namespace App\Actions;


use App\Models\FounderProfile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Update the positions that the founder would prefer in entrepreneurs
 * Class UpdatePfdPositions
 * @package App\Actions
 */
class UpdatePfdPositions
{
    /**
     * @throws Exception
     */
    public static function execute(FounderProfile $fdrProfile, array $data)
    {
        //ExcOnUserTypeMismatch::execute($fdrProfile->user, User::FOUNDER);
        $data = array_map(fn($value) => ['position_id' => $value], array_unique($data));
        if (count($data)) {
            DB::transaction(function () use ($fdrProfile, $data) {
                $fdrProfile->pfdPositions()->delete();
                $fdrProfile->pfdPositions()->createMany($data);
            });
        }
    }
}
