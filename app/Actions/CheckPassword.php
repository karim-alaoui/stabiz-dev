<?php


namespace App\Actions;


use App\Models\Staff;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

/**
 * Check password and returns bool
 * Class CheckPassword
 * @package App\Actions
 */
class CheckPassword
{
    /**
     * @param User|Staff $user
     * @param $password
     * @param bool $throwExceptionOnMismatch - throw Exception if the password doesn't match
     * @return bool
     * @throws AuthenticationException
     */
    public static function execute(User|Staff $user, $password, bool $throwExceptionOnMismatch = false): bool
    {
        $verify = Hash::check($password, $user->password);
        if (!$verify && $throwExceptionOnMismatch) {
            throw new AuthenticationException();
        }
        return $verify;
    }
}
