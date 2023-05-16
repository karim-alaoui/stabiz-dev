<?php


namespace App\Actions;


use App\Models\Application;
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
        $applications = Application::query();

        $type = strtolower(Arr::get($query, 'type'));
        if ($type == 'rejected') $applications = $applications->rejected();
        elseif ($type == 'accepted') $applications = $applications->accepted();

        return $applications->where('applied_to_user_id', $user->id)
            ->with('appliedBy:id,first_name,last_name')
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }
}
