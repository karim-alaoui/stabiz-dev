<?php


namespace App\Actions;


use App\Exceptions\ActionValidationException;
use App\Models\IncomeRange;
use App\Models\User;
use App\Rules\ValidDate;
use App\Traits\RelationshipTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateUser
 * @package App\Actions
 */
class UpdateUser
{
    use RelationshipTrait;

    /**
     * @param User|Authenticatable $user
     * @param array $data
     * @return Authenticatable|User
     * @throws ActionValidationException
     */
    public static function execute(User|Authenticatable $user, array $data): Authenticatable|User
    {
        

        DB::transaction(function () use ($user, $data) {
            $user->save();
            $user->load('income');
            if ($user->type == User::ENTR) {
                UpdateEntrProfile::execute($user, $data);
                $user->load('entrProfileWithRelations');
            }
        });

        return $user;
    }
}
