<?php

namespace App\Policies;

use App\Models\Staff;
use App\Models\UploadedDoc;
use Illuminate\Auth\Access\HandlesAuthorization;

class UploadedDocPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param $user
     * @param UploadedDoc $doc
     * @return bool|null
     */
    public function operateOnDoc($user, UploadedDoc $doc): ?bool
    {
        // only staff or the user who owns the document can operate on that document
        // operate means deleting the document or seeing it here
        if ($user instanceof Staff || $doc->user_id == $user->id) return true;
        return null;
    }
}
