<?php


namespace App\Actions;


use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Delete user display pic
 * Class DeleteDP
 * @package App\Actions
 */
class DeleteDP
{
    /**
     * @param User $user
     * @return User
     */
    public static function execute(User $user): User
    {
        $existing = $user->dp_full_path;
        if ($existing) {
            Storage::disk($user->dp_disk)
                ->delete($existing);
            $user->dp_disk = null;
            $user->dp_full_path = null;
            $user->save();
        }

        return $user;
    }
}
