<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TransactionNotFoundException extends Exception
{
    protected $code = 404;

    public function __construct(string $message = "Transaction error", int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
