<?php

namespace App\Policies;

use App\Models\Staff;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffPolicy
{
    use HandlesAuthorization;

    /**
     * The staff can update his/her own password.
     * Super admin can do anything
     * @param Staff $authenticatedStaff
     * @param Staff $toUpdateStaff
     * @return bool|null
     */
    public function updatePassword(Staff $authenticatedStaff, Staff $toUpdateStaff): bool|null
    {
        if ($toUpdateStaff->id === $authenticatedStaff->id) return true;
        return null;
    }
}
