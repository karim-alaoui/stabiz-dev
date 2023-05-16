<?php


namespace App\Actions;


use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Update user's display picture/profile pic/avatar
 * Class UpdateDP
 * @package App\Actions
 */
class UpdateDP
{
    /**
     * @param User $user
     * @param UploadedFile|null $file
     * @return User|null
     */
    public static function execute(User $user, UploadedFile $file = null): ?User
    {
        if (is_null($file)) return null;

        $path = Storage::put('dp', $file, 'public');

        DeleteDP::execute($user);

        $user->dp_full_path = $path;
        $user->dp_disk = config('filesystems.default');
        $user->save();

        return $user;
    }
}
