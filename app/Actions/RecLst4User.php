<?php

namespace App\Actions;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * Recommended user list for the user
 *
 * A user can see his/her recommendation list a.k.a
 * users recommended to him/her
 */
class RecLst4User
{
    public static function execute(User $user, array $query = []): LengthAwarePaginator
    {
        return Recommendation::query()
            ->where('recommended_to_user_id', $user->id)
            // in our database, user table is soft deleted
            // when you get the recommendation list, even though the user is deleted still
            // it would include that user id in the result. To make sure that it doesn't
            // include that, add this has check
            ->has('recommendedUser')
            ->with('recommendedUser:id,first_name,last_name,first_name_cana,last_name_cana,email,dob')
            ->latest()
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }
}
