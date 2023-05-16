<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;

/**
 * When we validate data in any Action class,
 * if the validation fails, we raise this error.
 * This makes it easy to understand where the validation exception actually
 * occurred. In fact, it accepts validator as argument just like Request does.
 * We use this to manipulate the error msg in Handler.php file.
 * Class ActionValidationException
 * @package App\Exceptions
 */
class ActionValidationException extends ValidationException
{
    /**
     * ActionValidationException constructor.
     * @param $validator
     */
    public function __construct($validator)
    {
        parent::__construct($validator);
    }
}
