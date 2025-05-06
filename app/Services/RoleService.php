<?php

namespace Pondra\PhpApiStarterKit\Services;

use Exception;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Models\Role;
use Pondra\PhpApiStarterKit\Repositories\RoleRepository;
use Pondra\PhpApiStarterKit\Requests\RoleStoreRequest;
use Pondra\PhpApiStarterKit\Validations\RoleStoreValidation;
use Ramsey\Uuid\Uuid;

class RoleService
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        RoleStoreValidation::setRoleRepository($roleRepository);
    }

    public function getRoles()
    {
        $roles = $this->roleRepository->getAllRoles();

        $message = $roles === null ? 
            'No role data has been created yet.' : 
            'All roles retrieved successfully.';

        return [
            'message' => $message,
            'data' => $roles
        ];
    }

    public function getRole(string $id)
    {
        $role = $this->roleRepository->findById($id);

        if ($role === null) {
            throw new ValidationException(null, 'Role not found.', 404);
        }

        return [
            'message' => 'Role retrieved successfully.',
            'data' => $role
        ];
    }

    public function createRole(RoleStoreRequest $request)
    {
        RoleStoreValidation::validation($request);

        try {
            Database::beginTransaction();

            date_default_timezone_set("Asia/Jakarta");

            $role = new Role();
            $role->id = Uuid::uuid4();
            $role->name = $request->name;
            $role->slug = StringHelper::slug($request->name);
            $role->createdAt = date('Y-m-d H:i:s');
            $role->updatedAt = date('Y-m-d H:i:s');

            $this->roleRepository->save($role);

            Database::commitTransaction();

            return [
                'message' => 'Role successfully created.',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug
                ]
            ];
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            throw $th;
        }
    }
}