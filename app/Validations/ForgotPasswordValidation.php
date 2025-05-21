<?php

namespace Pondra\PhpApiStarterKit\Validations;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Requests\ForgotPasswordRequest;

class ForgotPasswordValidation
{
    public static function validation(ForgotPasswordRequest $request): bool
    {
        $errors = [];

        $request->email = StringHelper::lower($request->email);

        if ($request->email == null) {
            $errors['email'][] = 'email is required.';
        } else if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'email format is invalid.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return true;
    }
}