<?php

namespace App\Policies;

use App\Models\AssignedUserToStaff;
use App\Models\Staff;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class AssignUserToStaffPolicy
 * @package App\Policies
 */
class AssignUserToStaffPolicy
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
     * @param Staff $staff
     * @param AssignedUserToStaff $assigned
     * @return bool|null
     */
    public function show(Staff $staff, AssignedUserToStaff $assigned): bool|null
    {
        if ($staff->id == $assigned->staff_id) return true;
        return null;
    }
}
