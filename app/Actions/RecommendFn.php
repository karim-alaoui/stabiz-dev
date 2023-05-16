<?php

namespace App\Actions;

use App\Exceptions\ActionException;
use App\Models\Recommendation;
use App\Models\Staff;
use App\Models\User;

/**
 * Recommend entr to founder and founder to entr
 */
class RecommendFn
{
    /**
     * @param Staff $staff
     * @param User $recommendUser
     * @param User $recommendToUser
     * @return mixed
     * @throws ActionException
     */
    public static function execute(Staff $staff, User $recommendUser, User $recommendToUser): mixed
    {
        if (strtolower($recommendUser->type) == strtolower($recommendToUser->type)) {
            $type = $recommendToUser->type;
            if ($type == User::ENTR) {
                $msg = __('Can not recommend entrepreneur to another entrepreneur');
            } else {
                $msg = __('Can not recommend founder to another founder');
            }
            throw new ActionException($msg);
        }

        $recommended = Recommendation::query()
            ->where('recommended_to_user_id', $recommendToUser->id)
            ->where('recommended_user_id', $recommendUser->id)
            ->first();

        if ($recommended) {
            $msg = __('These users are already recommended to each other');
            throw new ActionException($msg);
        }

        return Recommendation::create([
            'recommended_user_id' => $recommendUser->id,
            'recommended_to_user_id' => $recommendToUser->id,
            'by_staff_id' => $staff->id
        ]);
    }
}
