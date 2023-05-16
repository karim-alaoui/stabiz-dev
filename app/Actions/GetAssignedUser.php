<?php


namespace App\Actions;


use App\Models\AssignedUserToStaff;
use App\Models\Staff;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * Get assigned users to staff
 * Class GetAssignedUser
 * @package App\Actions
 */
class GetAssignedUser
{
    /**
     * @param Builder $assignedQuery
     * @param array $query
     * @return Builder
     */
    protected static function searchUsers(Builder $assignedQuery, array $query): Builder
    {
        return $assignedQuery->whereHas('user', function (Builder $q) use ($query) {
            $fname = Arr::get($query, 'first_name_u');
            if ($fname) $q->where('first_name', 'ilike', "%$fname%");

            $lname = Arr::get($query, 'last_name_u');
            if ($lname) $q->where('last_name', 'ilike', "%$lname%");

            $email = Arr::get($query, 'email');
            if ($email) $q->where('email', 'ilike', "%$email%");

            $gender = Arr::get($query, 'gender_u');
            if ($gender) $q->where('gender', "$gender");

            $type = Arr::get($query, 'type');
            if ($type) $q->where('type', strtolower($type));
        });
    }

    /**
     * @param Builder $assignedQuery
     * @param array $query
     * @return Builder
     */
    protected static function searchStaff(Builder $assignedQuery, array $query): Builder
    {
        return $assignedQuery->whereHas('staff', function (Builder $q) use ($query) {
            $fname = Arr::get($query, 'first_name_s');
            if ($fname) $q->where('first_name', 'ilike', "%$fname%");

            $lname = Arr::get($query, 'last_name_s');
            if ($lname) $q->where('last_name', 'ilike', "%$lname%");
        });
    }

    /**
     * @param Staff $staff
     * @param array $query
     * @return LengthAwarePaginator
     */
    public static function execute(Staff $staff, array $query): LengthAwarePaginator
    {
        $assigned = AssignedUserToStaff::query();

        $userId = Arr::get($query, 'user_id');
        if ($userId && !is_numeric($userId) && gettype($userId) == 'string') {
            $assigned = $assigned->whereIn('user_id', explode(',', $userId)); // user id is comma separated string
        }

        $staffId = Arr::get($query, 'staff_id');
        // only super admin can get the assigned users of other staff
        if ($staffId && gettype($staffId) == 'string' && $staff->hasRole(Staff::SUPER_ADMIN_ROLE)) {
            $assigned = $assigned->whereIn('staff_id', explode(',', $staffId)); // same as user id
        } elseif ($staff->hasRole(Staff::MATCH_MAKER_ROLE)) {
            $assigned = $assigned->where('staff_id', $staff->id);
        }

        $assigned = self::searchUsers($assigned, $query);
        $assigned = self::searchStaff($assigned, $query);

        return $assigned
            ->with([
                'user:id,first_name,last_name,gender',
                'staff:id,first_name,last_name'
            ])
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }
}
