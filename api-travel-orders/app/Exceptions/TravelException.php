<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TravelException extends Exception
{
    protected array $data;

    public function __construct(string $message, int $code = 500, Throwable $previous = null, $data = [])
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    function getData(): array
    {
        return $this->data;
    }
}
