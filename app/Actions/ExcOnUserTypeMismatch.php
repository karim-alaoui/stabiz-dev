<?php


namespace App\Actions;


use App\Models\User;
use Exception;

/**
 * Throw exception if the user doesn't match the
 * given user type
 * class \App\Actions\ExcOnUserTypeMismatch
 * @package App\Actions
 */
class ExcOnUserTypeMismatch
{
    /**
     * @param User $user
     * @param $type
     * @throws Exception
     */
    public static function execute(User $user, $type)
    {
        if (strtolower($user->type) != strtolower($type)) {
            throw new Exception(__('User is not ' . $type));
        }
    }
}
