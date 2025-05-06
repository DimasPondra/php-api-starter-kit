<?php

namespace Pondra\PhpApiStarterKit\Validations;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Repositories\RoleRepository;
use Pondra\PhpApiStarterKit\Requests\RoleStoreRequest;

class RoleStoreValidation
{
    private static RoleRepository $roleRepository;

    public static function setRoleRepository(RoleRepository $roleRepository)
    {
        self::$roleRepository = $roleRepository;
    }

    public static function validation(RoleStoreRequest $request): bool
    {
        $errors = [];

        $request->name = StringHelper::capitalize($request->name);

        if ($request->name == null) {
            $errors['name'][] = 'name is required.';
        } else if (strlen($request->name) > 255) {
            $errors['name'][] = 'name to long, max 255 characters.';
        } else if (self::$roleRepository->findByName($request->name) !== null) {
            $errors['name'][] = 'name already exists.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return true;
    }
}