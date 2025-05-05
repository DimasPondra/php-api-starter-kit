<?php

namespace Pondra\PhpApiStarterKit\Controllers;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Requests\RoleStoreRequest;
use Pondra\PhpApiStarterKit\Services\RoleService;

class RoleController
{
    private RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function store()
    {
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, true);

        $request = new RoleStoreRequest();
        $request->name = $data['name'] ?? null;

        try {
            $response = $this->roleService->createRole($request);
            
            ResponseHelper::success($response['message'], $response['data']);
        } catch (ValidationException $ve) {
            ResponseHelper::error(
                $ve->getMessage(), 
                $ve->getErrors(), 
                $ve->getCode(), 
                $ve->getStatusCode()
            );
        } catch (\Throwable $th) {
            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }
}