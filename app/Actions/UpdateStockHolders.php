<?php


namespace App\Actions;


use App\Models\FounderProfile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Update major stock holders of founder
 * Class UpdateStockHolders
 * @package App\Actions
 */
class UpdateStockHolders
{
    /**
     * @param FounderProfile $fdrProfile
     * @param array $data
     * @throws Exception
     */
    public static function execute(FounderProfile $fdrProfile, array $data)
    {
        //ExcOnUserTypeMismatch::execute($fdrProfile->user, User::FOUNDER);
        $data = array_map(fn($value) => ['name' => $value], array_unique($data));
        if (count($data)) {
            DB::transaction(function () use ($fdrProfile, $data) {
                $fdrProfile->majorStockHolders()->delete();
                $fdrProfile->majorStockHolders()->createMany($data);
            });
        }
    }
}
