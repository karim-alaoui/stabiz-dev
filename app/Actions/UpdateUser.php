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
        $validator = Validator::make($data, [
            'dob' => ['nullable', new ValidDate()],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'income_range_id' => ['nullable', 'exists:' . (new IncomeRange())->getTable() . ',id']
        ]);

        if ($validator->fails()) throw new ActionValidationException($validator);

        $update = [
            'first_name',
            'last_name',
            'dob',
            'gender',
            'income_range_id',
            'first_name_cana',
            'last_name_cana'
        ];
        foreach ($update as $column) {
            $value = Arr::get($data, $column);
            if (Arr::exists($data, $column) && !is_null($value)) $user->{$column} = $value;
        }

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
