<?php

namespace Pondra\PhpApiStarterKit\Services;

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
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
    }

    public function create(RoleStoreRequest $request)
    {
        RoleStoreValidation::validation($request);

        try {
            Database::beginTransaction();

            date_default_timezone_set("Asia/Jakarta");

            $role = new Role();
            $role->id = Uuid::uuid4();
            $role->name = $request->name;
            $role->slug = $request->name;
            $role->createdAt = date('Y-m-d H:i:s');
            $role->updatedAt = date('Y-m-d H:i:s');

            $this->roleRepository->save($role);

            Database::commitTransaction();

            $response = ResponseHelper::success('Role successfully created.', [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug
            ]);

            return $response;
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            $response = ResponseHelper::error('Something went wrong.', $th->getMessage());

            return $response;
        }
    }
}