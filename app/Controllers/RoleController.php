<?php

namespace Pondra\PhpApiStarterKit\Controllers;

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Repositories\RoleRepository;
use Pondra\PhpApiStarterKit\Requests\RoleStoreRequest;
use Pondra\PhpApiStarterKit\Services\RoleService;

class RoleController
{
    private RoleService $roleService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $roleRepository = new RoleRepository($connection);
        $this->roleService = new RoleService($roleRepository);
    }

    public function store()
    {
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, true);

        $request = new RoleStoreRequest();
        $request->name = $data['name'] ?? null;

        $this->roleService->create($request);
    }
}