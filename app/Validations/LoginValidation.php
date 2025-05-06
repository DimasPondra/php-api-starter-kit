<?php

namespace Pondra\PhpApiStarterKit\Validations;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Requests\LoginRequest;

class LoginValidation
{
    public static function validation(LoginRequest $request): bool
    {
        $errors = [];

        $request->email = StringHelper::lower($request->email);

        if ($request->email == null) {
            $errors['email'][] = 'email is required.';
        } else if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'email format is invalid.';
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