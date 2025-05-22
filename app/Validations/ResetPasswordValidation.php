<?php

namespace Pondra\PhpApiStarterKit\Validations;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Requests\ResetPasswordRequest;

class ResetPasswordValidation
{
    public static function validation(ResetPasswordRequest $request): bool
    {
        $errors = [];

        if ($request->token == null || trim($request->token) == null) {
            $errors['token'][] = 'token is required.';
        } else if (strlen($request->token) > 64) {
            $errors['token'][] = 'token to long, max 64 characters.';
        }

        if ($request->password == null || trim($request->password) == null) {
            $errors['password'][] = 'password is required.';
        } else if (strlen($request->password) < 6) {
            $errors['password'][] = 'password must be at least 6 characters.';
        } else if (strlen($request->password) > 16) {
            $errors['password'][] = 'password to long, max 16 characters.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return true;
    }
}