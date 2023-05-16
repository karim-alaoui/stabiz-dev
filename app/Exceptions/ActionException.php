<?php

namespace App\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Return this exception any exception from any action
 * from app\Actions folder.
 * In the future, we might add methods into it
 * to perform certain things if needed
 * Class ActionException
 * @package App\Exceptions
 */
class ActionException extends Exception
{
    /**
     * ActionException constructor.
     * @param $message
     * @param int $code
     * @param Throwable|null $previous
     */
    #[Pure] public function __construct($message, $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
