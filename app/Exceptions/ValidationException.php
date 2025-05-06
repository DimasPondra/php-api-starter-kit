<?php

namespace Pondra\PhpApiStarterKit\Exceptions;

use Exception;

class ValidationException extends Exception
{
    private ?array $errors;
    private string $statusCode = 'Unprocessable Entity';

    public function __construct(?array $errors, $message = "Validation failed.", $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getStatusCode(): string
    {
        if ($this->code === 404) {
            $this->statusCode = 'Not Found';
        }

        return $this->statusCode;
    }
}