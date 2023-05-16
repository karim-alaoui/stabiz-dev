<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Models\OTP;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class VerifyOTP
 * @package App\Actions
 */
class VerifyOTP
{
    /**
     * @param string $email
     * @param int $otp
     * @param string $userType
     * @param bool $markMailVerified
     * @return mixed
     * @throws ActionException
     */
    public static function execute(string $email, int $otp, string $userType, bool $markMailVerified = false): void
    {
        DB::beginTransaction();
        $email = strtolower($email);
        $otpRecord = OTP::where('email', $email)
            ->notVerified()
            ->latest()
            ->first();

        if (!$otpRecord) throw new ActionException(__('message.otp_verify_failed'));
        if (Hash::check($otp, $otpRecord->otp)) {
            $otpRecord->verified_at = now();
            $otpRecord->save();

            if ($markMailVerified) {
                $user = User::emailAndType($email, $userType)->first();
                if ($user && is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                    $user->save();
                }
            }

            DB::commit();
            return;
        }
        DB::rollBack();
        throw new ActionException(__('message.otp_verify_failed'));
    }
}
