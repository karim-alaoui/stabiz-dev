<?php


namespace App\Actions;


use App\Models\Staff;
use Illuminate\Support\Facades\Auth;

/**
 * Assign users to staff
 * Class AssignUsers
 * @package App\Actions
 */
class AssignUsers
{
    /**
     * @param Staff $staff
     * @param array $userIds
     */
    public static function execute(Staff $staff, array $userIds)
    {
        // make sure there's no duplicate
        $alreadyAssignedUser = $staff->assignedUsers()->select('user_id')->get()->pluck('user_id');
        $userIds = collect($userIds)->diff($alreadyAssignedUser)->values()->toArray(); // make sure to exclude the ones which already exist
        $staffId = Auth::guard('api-staff')->check() ? Auth::guard('api-staff')->id() : null;
        $data = array_map(fn($userId) => ['user_id' => $userId, 'added_by_staff_id' => $staffId], array_unique($userIds));

        if (count($data)) $staff->assignedUsers()->createMany($data);
    }
}
