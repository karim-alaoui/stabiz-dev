<?php

namespace App\Rules;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Rule;

/**
 * Check if the value is a valid date or datetime
 * Class ValidateDateTime
 * @package App\Rules
 */
class ValidateDateTime implements Rule
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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $valid = false;
        try {
            Carbon::parse($value); // this would throw error if invalid format provided
            $valid = true;
        } catch (Exception) {
        }
        return $valid;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans(__('validation.valid_date'));
    }
}
