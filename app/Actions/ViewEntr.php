<?php


namespace App\Actions;


use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * View all details of entrepreneur
 * Class ViewEntr
 * @package App\Actions
 */
class ViewEntr
{
    /**
     * @param User $entrepreneur
     * @return User
     * @throws Exception
     */
    public static function execute(User $entrepreneur): User
    {
        ExcOnUserTypeMismatch::execute($entrepreneur, User::ENTR);

        $entrepreneur->load('entrProfile');

        /**@var User $user */
        $user = Auth::user();
        if ($user->cant('viewAllEntrDetails', $entrepreneur)) {
            // to be done later
        }

        return $entrepreneur;
    }
}
