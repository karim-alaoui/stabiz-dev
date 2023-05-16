<?php

namespace App\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;

/**
 * The format has to be year-month-day format
 * Doesn't matter if the month/day is 2 digit prefixed by 0 or a single digit
 * and year is 2 digit or 4 digit
 */
class ValidDate implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $validDate = false;
        try {
            if (!is_string($value)) return false;
            $date = null;
            if (preg_match('/^(\d{2,4}-\d{1,2}-\d{1,2})$/', $value, $matches)) {
                $date = explode('-', $value);
            } elseif (preg_match('/^(\d{2,4}\/\d{1,2}\/\d{1,2})$/', $value)) {
                $date = explode('/', $value);
            }
            if ($date) $validDate = checkdate($date[1], $date[2], $date[0]);
        } catch (Exception) {
        }
        return $validDate;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.valid_date_formats');
    }
}
