<?php

namespace Pondra\PhpApiStarterKit\Validations;

use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Requests\RoleStoreRequest;

class RoleStoreValidation
{
    public static function validation(RoleStoreRequest $request): bool
    {
        $errors = [];

        if ($request->name == null || trim($request->name) == null) {
            $errors['name'][] = 'name is required.';
        } else if (strlen($request->name) > 255) {
            $errors['name'][] = 'name to long, max 255 characters.';
        }

        if (!empty($errors)) {
            ResponseHelper::error('Validation failed.', $errors, 422, 'Unprocessable Entity');
            exit();
        }

        return true;
    }
}