<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InsufficientFundsException extends Exception
{
    protected $code = 442;

    public function __construct(string $message = "Transaction error", int $code = 442, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
