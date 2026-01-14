<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class WalletException extends Exception
{
    protected $code = 404;

    public function __construct(string $message = "Wallet error", int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
