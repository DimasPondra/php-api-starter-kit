<?php

namespace Pondra\PhpApiStarterKit\Validations;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Requests\VerifyEmailRequest;

class VerifyEmailValidation
{
    public static function validation(VerifyEmailRequest $request): bool
    {
        $errors = [];

        if ($request->token == null || trim($request->token) == null) {
            $errors['token'][] = 'token is required.';
        } else if (strlen($request->token) > 64) {
            $errors['token'][] = 'token to long, max 64 characters.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return true;
    }
}