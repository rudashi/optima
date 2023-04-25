<?php

declare(strict_types=1);

namespace Rudashi\Optima\Exceptions;

use Throwable;
use UnexpectedValueException;

class IncorrectValueException extends UnexpectedValueException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code === 0 ? 422 : $code, $previous);
    }
}
