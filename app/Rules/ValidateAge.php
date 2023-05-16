<?php

namespace App\Rules;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Validate if the date is in that age range or not
 * Class ValidateAge
 * @package App\Rules
 */
class ValidateAge implements Rule
{
    protected string $msg;

    /**
     * Create a new rule instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(protected ?int $lowerLimit = null, protected ?int $upperLimit = null)
    {
        if (is_null($this->lowerLimit) && is_null($this->upperLimit)) {
            throw new Exception('Both upper and lower limit can not be null in age validation');
        }
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
        // first check if the value is valid date or datetime
        $validator = Validator::make(['value' => $value], [
            'value' => ['nullable', new ValidateDateTime()]
        ]);

        if ($validator->fails()) {
            $msg = __('validation.valid_datetime');
            /** @var string $msg */
            $this->msg = $msg;
            return false;
        }

        $now = now();
        $dob = Carbon::parse($value);
        $age = $now->diffInYears($dob);
        $lowerLimit = $this->lowerLimit;
        $upperLimit = $this->upperLimit;

        if ($lowerLimit && $age < $lowerLimit) {
            $msg = __('validation.age_lowerlimit', [
                'lower' => "$lowerLimit years"
            ]);
            /**@var string $msg */
            $this->msg = $msg;
            return false;
        } elseif ($upperLimit && $age > $upperLimit) {
            $msg = __('validation.age_upperlimit', [
                'upper' => "$upperLimit years"
            ]);
            /**@var string $msg */
            $this->msg = $msg;
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->msg;
    }
}
