<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TransactionException extends Exception
{
    protected $code = 400;

    public function __construct(string $message = "Transaction error", int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
