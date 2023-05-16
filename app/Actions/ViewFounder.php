<?php


namespace App\Actions;


use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * Class ViewFounder
 * @package App\Actions
 */
class ViewFounder
{
    /**
     * @param User $founder
     * @return User
     * @throws Exception
     */
    public static function execute(User $founder)
    {
        ExcOnUserTypeMismatch::execute($founder, User::FOUNDER);

        // fields that are masked and can only be seen if the user is a premium user
        $maskedFields = ['first_name', 'last_name', 'fdrProfile.company_name'];

        $founder->load('fdrProfile');

        /**@var User $user */
        $user = Auth::user();


        if ($user->cant('viewAllFdrDetail', $founder)) {
            // to be done later
        }

        return $founder;
    }
}
