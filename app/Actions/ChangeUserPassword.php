<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

/**
 * Class ChangeUserPassword
 * @package App\Actions
 */
class ChangeUserPassword
{
    /**
     * @param User $user
     * @param string $currentPass
     * @param string $newPass
     * @param bool $logoutEverywhere
     * @throws ActionException
     * @throws AuthenticationException
     */
    public static function execute(User $user, string $currentPass, string $newPass, bool $logoutEverywhere = false)
    {
        $match = CheckPassword::execute($user, $currentPass);
        if (!$match) throw new ActionException(__('Current password do not match'));

        if ($newPass == $currentPass) {
            throw new ActionException(__('New password and old password can not be same'));
        }

        $user->password = Hash::make($newPass);
        $user->save();

        if ($logoutEverywhere) LogoutEverywhere::execute($user);
    }
}
