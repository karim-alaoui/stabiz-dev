<?php

namespace App\Actions;

use App\Models\Staff;
use App\Models\UploadedDoc;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Storage;

/**
 * Returns a temporary url for the document
 * which is valid for only 5 mins (time limit can be changed)
 */
class UploadDocLink
{
    /**
     * @param UploadedDoc|int $uploadedDoc
     * @param User|Staff $user
     * @param int $validMins - for how long the link will be valid
     * @return string
     * @throws AuthorizationException
     */
    public static function execute(UploadedDoc|int $uploadedDoc, User|Staff $user, int $validMins = 5): string
    {
        if (gettype($uploadedDoc) == 'integer') {
            $uploadedDoc = UploadedDoc::findOrFail($uploadedDoc);
        }

        if ($user instanceof Staff || $uploadedDoc->user_id == $user->id) {
            return Storage::disk($uploadedDoc->file_disk)
                ->temporaryUrl(
                    $uploadedDoc->filepath, now()->addMinutes($validMins)
                );
        } else {
            throw new AuthorizationException();
        }
    }
}
