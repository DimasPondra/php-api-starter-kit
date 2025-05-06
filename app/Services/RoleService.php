<?php

namespace Pondra\PhpApiStarterKit\Services;

use Exception;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Models\Role;
use Pondra\PhpApiStarterKit\Repositories\RoleRepository;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Requests\RoleStoreRequest;
use Pondra\PhpApiStarterKit\Requests\RoleUpdateRequest;
use Pondra\PhpApiStarterKit\Validations\RoleStoreValidation;
use Pondra\PhpApiStarterKit\Validations\RoleUpdateValidation;
use Ramsey\Uuid\Uuid;

class RoleService
{
    private RoleRepository $roleRepository;
    private UserRepository $userRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        RoleStoreValidation::setRoleRepository($roleRepository);
        RoleUpdateValidation::setRoleRepository($roleRepository);

        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
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

    public function updateRole(RoleUpdateRequest $request, string $id)
    {
        $role = $this->roleRepository->findById($id);

        if ($role === null) {
            throw new ValidationException(null, 'Role not found.', 404);
        }

        RoleUpdateValidation::validation($request, $role);

        try {
            Database::beginTransaction();

            date_default_timezone_set("Asia/Jakarta");

            $role->name = $request->name;
            $role->slug = StringHelper::slug($request->name);
            $role->updatedAt = date('Y-m-d H:i:s');

            $this->roleRepository->update($role);

            Database::commitTransaction();

            return [
                'message' => 'Role successfully updated.',
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

    public function deleteRole(string $id)
    {
        $role = $this->roleRepository->findById($id);

        if ($role === null) {
            throw new ValidationException(null, 'Role not found.', 404);
        }

        $users = $this->userRepository->getAllUsers([
            'role_id' => $role->id,
        ]);

        if ($users !== null) {
            throw new ValidationException(null, 'Cannot delete. This record has existing relationships.', 400);
        }

        try {
            Database::beginTransaction();

            $this->roleRepository->deleteById($id);

            Database::commitTransaction();

            return [
                'message' => 'Role successfully deleted.',
                'data' => null
            ];
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            throw $th;
        }
    }
}