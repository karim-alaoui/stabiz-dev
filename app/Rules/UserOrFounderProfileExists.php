<?php

namespace App\Rules;
use App\Models\User;
use App\Models\FounderProfile;

use Illuminate\Contracts\Validation\Rule;

class UserOrFounderProfileExists implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return User::where('id', $value)->orWhereHas('founderProfile', function ($query) use ($value) {
            $query->where('user_id', $value);
        })->exists();
    }

    public function message()
    {
        return 'The selected :attribute is invalid.';
    }
}
