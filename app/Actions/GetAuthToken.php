<?php


namespace App\Actions;

use App\Models\Organizer;
use App\Models\Staff;
use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

/**
 * Generate an auth token
 * Class GetAuthToken
 * @package App\Actions
 */
class GetAuthToken
{
    protected static string $provider = 'users';

    /**
     * @param string $email
     * @param string $password
     * @param string|null $userType
     * @param string $provider - this means the provider under guard key in config/auth.php file. There are two values
     * staff and users for our application.
     * @return mixed
     * @throws AuthenticationException
     * @throws Exception
     */
    public static function execute(string $email, string $password, string $userType = null, string $provider = 'users'): mixed
    {
        if (!in_array($provider, ['users', 'staff', 'organizers'])) {
            throw new Exception('provider can be either user, staff, or organizers');
        }

        if (is_null($userType) && $provider == 'users') {
            throw new Exception('User type is needed when provider is users');
        }

        if ($provider == 'users') {
            $user = User::emailAndType($email, $userType)->latest()->first();
        } else if ($provider == 'staff') {
            $user = Staff::where('email', $email)->latest()->first();
        } else if ($provider == 'organizers'){
            $user = Organizer::where('email', $email)->latest()->first();
        }

        $defaultMsg = __('message.login_failed');

        if (!$user) {
            throw new AuthenticationException($defaultMsg);
        }

        if (!Hash::check($password, $user->password)) {
            throw new AuthenticationException($defaultMsg);
        }

        return $user->createToken('login');
    }
}
