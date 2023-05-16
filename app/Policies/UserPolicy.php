<?php /** @noinspection PhpUnused */

namespace App\Policies;

use App\Models\Package;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class UserPolicy
 * @package App\Policies
 */
class UserPolicy
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
     * Who can search for founders
     * Only entr can
     * @param User $user
     * @return bool|null
     * @noinspection PhpUnused
     */
    public function searchFdr(User $user): ?bool
    {
        if ($user->type == User::ENTR) return true;
        return null;
    }

    /**
     * @param $user
     * @param User $editUser
     * @return bool|null
     */
    public function update($user, User $editUser): bool|null
    {
        if ($user instanceof User && $user->id == $editUser->user) return true;
        return null; // important to return null
    }


    /**
     * Who can search for entrepreneurs
     * @param User $user
     * @return bool|null
     */
    public function searchEntr(User $user): ?bool
    {
        if ($user->type == User::FOUNDER) return true;
        return null;
    }

    /**
     * @param User $user
     * @return bool|null
     */
    public function updateEntr(User $user): ?bool
    {
        if ($user->type == User::ENTR) return true;
        return null;
    }

    /**
     * @param User $user
     * @return bool|null
     */
    public function updateFdr(User $user): ?bool
    {
        if ($user->type == User::FOUNDER) return true;
        return null;
    }

    /**
     * View founder
     * @param User $user
     * @param User $founderUser
     * @return bool|null
     */
    public
    function viewFounder(User $user, User $founderUser): ?bool
    {
        if ($user->type == User::ENTR && $founderUser->type == User::FOUNDER) return true;
        return null;
    }

    /**
     * Check if the user can see all the details of the founder
     * User must have active subscription to view it
     * @param User $user
     * @param User $founderUser
     * @return bool|null
     */
    public
    function viewAllFdrDetail(User $user, User $founderUser): ?bool
    {
        if ($this->viewFounder($user, $founderUser) && $user->subscribed(Package::PREMIUM)) return true;
        return null;
    }

    /**
     * @param User $user
     * @param User $entrepreneur
     * @return bool|null
     */
    public
    function viewEntrepreneur(User $user, User $entrepreneur): ?bool
    {
        if ($user->type == User::FOUNDER && $entrepreneur->type == User::ENTR) return true;
        return null;
    }

    /**
     * @param User $user
     * @param User $entrepreneur
     * @return bool|null
     */
    public
    function viewAllEntrDetails(User $user, User $entrepreneur): ?bool
    {
        if ($this->viewEntrepreneur($user, $entrepreneur) && $user->subscribed(Package::PREMIUM)) return true;
        return null;
    }
}
