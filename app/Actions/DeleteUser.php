<?php


namespace App\Actions;


use App\Models\DeletedUser;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Delete an user's account
 * After the user deletes the account, we will delete all the personal information
 * apart from email, email is kept to track if the email is used to create account again.
 * (apart from that, email is unique in the table)
 * since after user deletes the account, we other data of the user and not hard delete the user
 * Class DeleteUser
 * @package App\Actions
 */
class DeleteUser
{
    /**
     * @param User|Staff $user
     * @throws AuthorizationException
     */
    public static function execute(User|Staff $user)
    {
        /**@var Staff|User $authuser */
        $authuser = Auth::user();

        switch (true) {
            case ($authuser instanceof User || $authuser instanceof Staff) && $user->id == $authuser->id: // user can one's own account
            case $authuser instanceof Staff && $authuser->hasRole(Staff::SUPER_ADMIN_ROLE): // or superadmin
                break;
            default:
                throw new AuthorizationException();
        }



        DB::transaction(function () use ($user) {
            if ($user instanceof User) {
                // keep track of deleted emails
                DeletedUser::create([
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }

            LogoutEverywhere::execute($user);

            // delete the personal info if user
            if ($user instanceof User) {
                // we keep the email as null
                // because on database there's unique constraint on the email column
                // and the same email could be used again to create an account
                // Thus we keep the email in a separate table to keep track
                // if same email is used again to create an account or not
                $user->update([
                    'email' => null,
                    'first_name' => null,
                    'last_name' => null,
                    'dob' => null,
                    'gender' => null
                ]);
            }

            $user->delete(); // soft delete the user/staff
        });
    }
}
