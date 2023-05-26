<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

/**
 * Class CreateUser
 * @package App\Actions
 */
class CreateUser
{
    /**
     * @param array $data
     * @param string $type
     * @param bool $sendOTP
     * @return mixed
     * @throws ActionException
     */
    public static function execute(array $data, string $type, bool $sendOTP = false): mixed
    {
        if (!in_array($type, ['entrepreneur'])) {
            throw new ActionException('Type has to be entrepreneur');
        }
        $email = strtolower(Arr::get($data, 'email'));
        $userdata = Arr::except($data, ['password', 'email']);
        $userdata['email'] = $email;
        $userdata['password'] = Hash::make(Arr::get($data, 'password'));
        $userdata['type'] = $type;
        $user = User::create($userdata);
        if ($sendOTP) {
            $subject = __('message.otp_sent_reg');
            SendOTP::execute($email, $subject);
        }
        return $user;
    }
}
