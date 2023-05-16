<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Reset password for users only. Not staff
 * Class ResetPass
 * @package App\Actions
 */
class ResetPass
{
    /**
     * Entire flow works like
     *  ----> User requests for OTP
     * -----> doesn't matter if the user exists in the system or not, we show that the user
     * will get otp (done for security reason inspired from django framework)
     * ------> Then call this endpoint to verify the otp and reset the password
     * If the user didn't exist, no otp mail would be sent to the user
     * Thus it fails on this page, since the user has no otp
     * @param string $email
     * @param string|int $otp
     * @param string $newPass
     * @param string $userType
     * @return mixed
     * @throws ActionException
     * @throws AuthenticationException
     */
    public static function execute(string $email, string|int $otp, string $newPass, string $userType): mixed
    {
        DB::beginTransaction();
        try {
            VerifyOTP::execute($email, $otp, $userType, true);
        } catch (ActionException) {
            throw new ActionException(__('message.otp_verify_failed'), 401);
        }

        $user = User::emailAndType($email, $userType)->first();
        if (!$user) {
            throw new ActionException(__('Password reset failed'), 401);
        }

        LogoutEverywhere::execute($user); // logout the user everywhere
        // reset the password
        $user->password = Hash::make($newPass);
        $user->save();

        $token = GetAuthToken::execute($email, $newPass, $userType);
        DB::commit();

        return $token;
    }
}
