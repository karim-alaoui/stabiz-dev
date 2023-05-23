<?php


namespace App\Actions;


use App\Models\Application;
use App\Models\FounderProfile;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * Get received applications
 * Class GetRecvdApl
 * @package App\Actions
 */
class GetRecvdApl
{
    public static function execute(User|Authenticatable $user, array $query): LengthAwarePaginator
    {
        $userId = $user->id;
        if($user->type == "founder"){
            $founderProfile = FounderProfile::join('founder_user', 'founder_profiles.id', '=', 'founder_user.founder_id')
            ->where('founder_user.user_id', $user->id)
            ->first();

            if (!$founderProfile) {
                return response()->json(['message' => 'Founder profile not found'], 404);
            }else{
                $userId = $founderProfile->id;
            }
        }
        
        $applications = Application::query();

        $type = strtolower(Arr::get($query, 'type'));
        if ($type == 'rejected') $applications = $applications->rejected();
        elseif ($type == 'accepted') $applications = $applications->accepted();

        return $applications->where('applied_to_user_id', $userId)
            ->with('appliedBy:id,first_name,last_name')
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }
}
