<?php

namespace App\Rules;

use App\Models\User;
use Exception;
use Illuminate\Contracts\Validation\Rule;

/**
 * Same email can be used for both founder and entrepreneur user types
 * So, an email has to be unique for user type
 * So, for example,
 * user@example.com can be used for a founder user and an entrepreneur user
 * After that email can not be used again for the same type.
 * if user@example.com is registered for founder type only and not for entrepreneur type
 * then this email can't be used to register founder user and can only be used to register
 * entrepreneur user
 * @UniqueEmailForUserType
 */
class UniqueEmailForUserType implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(protected ?string $type)
    {
        $this->type = strtolower($type);
        $types = [User::FOUNDER, User::ENTR];
        if (!in_array($this->type, $types)) {
            throw new Exception(__('Invalid user type'));
        }
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
        $type = strtolower($this->type);
        return !(bool)User::query()
            ->where([
                ['email', 'ilike', $value],
                ['type', 'ilike', $type]
            ])->first();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('The email has already been taken.');
    }
}
