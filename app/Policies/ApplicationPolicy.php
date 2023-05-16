<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use JetBrains\PhpStorm\Pure;

/**
 * Class ApplicationPolicy
 * @package App\Policies
 */
class ApplicationPolicy
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
     * Accept an application
     *
     * Only accept if the application was applied to the user
     * @param User $user
     * @param Application $application
     * @return bool|null
     */
    public function acceptApplication(User $user, Application $application): ?bool
    {
        if ($user->id == $application->applied_to_user_id) return true;
        return null;
    }

    /**
     * @param User $user
     * @param Application $application
     * @return bool|null
     */
    #[Pure] public function rejectApplication(User $user, Application $application): ?bool
    {
        return $this->acceptApplication($user, $application);
    }

    /**
     * If the user can get details of an applicant user.
     * @param User $user
     * @param Application $application
     * @return bool|null
     */
    #[Pure] public function getApplicantDetails(User $user, Application $application): ?bool
    {
        return $this->acceptApplication($user, $application);
    }
}
