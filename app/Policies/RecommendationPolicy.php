<?php

namespace App\Policies;

use App\Models\Staff;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class RecommendationPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param $user
     * @return bool|null
     */
    public function recommend($user): ?bool
    {
        Log::info(json_encode($user));
        Log::info(get_class($user));
        if ($user instanceof Staff) return true;
        return null;
    }
}
