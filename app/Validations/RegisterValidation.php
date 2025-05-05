<?php

namespace Pondra\PhpApiStarterKit\Validations;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Requests\RegisterRequest;

class RegisterValidation
{
    private static UserRepository $userRepository;

    public static function setUserRepository(UserRepository $userRepository): void
    {
        self::$userRepository = $userRepository;
    }

    public static function validation(RegisterRequest $request): bool
    {
        $errors = [];

        if ($request->name == null || trim($request->name) == null) {
            $errors['name'][] = 'name is required.';
        } else if (strlen($request->name) > 255) {
            $errors['name'][] = 'name to long, max 255 characters.';
        }

        if ($request->email == null || trim($request->email) == null) {
            $errors['email'][] = 'email is required.';
        } else if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'email format is invalid.';
        } else if (self::$userRepository->findByEmail($request->email) !== null) {
            $errors['email'][] = 'email already exists.';
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