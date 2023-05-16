<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * strip all the html tags to see if there's actually any description or not
 * sometimes, for text editor add some html tags by default even if it's empty
 * so sending just a bunch of tags won't work if there's no content in the html.
 * Eg - <p></p> won't work. <p>paragraph</p> will work.
 * Class ValidateEmptyHTML
 * @package App\Rules
 */
class ValidateEmptyHTML implements Rule
{
    protected string $attribute;

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
    public function passes($attribute, $value): bool
    {
        $this->attribute = $attribute;
        $value = preg_replace('/<(?>\/?)[^>]*>/i', '', $value);
        $value = trim($value);
        return (bool)$value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.empty_html', ['attribute' => $this->attribute]);
    }
}
