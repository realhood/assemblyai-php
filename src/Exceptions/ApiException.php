<?php

namespace Realhood\AssemblyAI\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    protected $statusCode;

    public function __construct(string $message = "", int $statusCode = 0, Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
