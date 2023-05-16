<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Models\OTP;
use App\Notifications\OTPCodeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

/**
 * Class SendOTP
 * @package App\Actions
 */
class SendOTP
{
    /**
     * @param string $email
     * @param string|null $subject
     * @param bool $registration - if the OTP is requested during registration. The subject will change based on that.
     * @return void
     * @throws ActionException
     */
    public static function execute(string $email, string $subject = null, bool $registration = false): void
    {
        $otp = mt_rand(100000, 999999); // 6 digit OTP
        $email = strtolower($email);

        $requested = OTP::query()
            ->select('id')
            ->where('email', 'ilike', $email)
            ->where('created_at', '>=', now()->subMinute()->format('Y-m-d H:i:s'))
            ->count();
        if ($requested >= 2) {
            $msg = __('Please wait a minute before requesting again');
            throw new ActionException($msg);
        }

        DB::transaction(function () use ($email, $otp) {
            /**
             * Mark the all the otps that the user requested for
             * but never verified (used) as invalid.
             */
            OTP::where('email', 'ilike', $email)
                ->notVerified()
                ->update([
                    'is_invalid' => DB::raw('false')
                ]);

            $otp = Hash::make($otp);
            OTP::create([
                'email' => $email,
                'expired_at' => now()->addMinutes(5),
                'otp' => $otp,
            ]);
        });

        if ($registration) $subject = __('message.otp_sent_reg');
        Notification::route('mail', $email)
            ->notify(new OTPCodeNotification($otp, $subject));
    }
}
