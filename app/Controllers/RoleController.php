<?php

namespace Pondra\PhpApiStarterKit\Controllers;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\LoggerHelper;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Requests\RoleStoreRequest;
use Pondra\PhpApiStarterKit\Requests\RoleUpdateRequest;
use Pondra\PhpApiStarterKit\Services\RoleService;

class RoleController
{
    private RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        try {
            $response = $this->roleService->getRoles();
            
            ResponseHelper::success($response['message'], $response['data']);
        } catch (\Throwable $th) {
            ResponseHelper::error('Something went wrong, Please try again.');
        }
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
            LoggerHelper::emergency('Failed to create new role.', [
                'error' => $th->getMessage()
            ]);

            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }

    public function show(string $id)
    {
        try {
            $response = $this->roleService->getRole($id);
            
            ResponseHelper::success($response['message'], $response['data']);
        } catch (ValidationException $ve) {
            ResponseHelper::error(
                $ve->getMessage(), 
                $ve->getErrors(), 
                $ve->getCode(), 
                $ve->getStatusCode()
            );
        } catch (\Throwable $th) {
            LoggerHelper::emergency('Failed to get a role.', [
                'error' => $th->getMessage()
            ]);

            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }

    public function update(string $id)
    {
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, true);

        $request = new RoleUpdateRequest();
        $request->name = $data['name'] ?? null;

        try {
            $response = $this->roleService->updateRole($request, $id);
            
            ResponseHelper::success($response['message'], $response['data']);
        } catch (ValidationException $ve) {
            ResponseHelper::error(
                $ve->getMessage(), 
                $ve->getErrors(), 
                $ve->getCode(), 
                $ve->getStatusCode()
            );
        } catch (\Throwable $th) {
            LoggerHelper::emergency('Failed to update a role.', [
                'error' => $th->getMessage()
            ]);

            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }

    public function delete(string $id)
    {
        try {
            $response = $this->roleService->deleteRole($id);
            
            ResponseHelper::success($response['message'], $response['data']);
        } catch (ValidationException $ve) {
            ResponseHelper::error(
                $ve->getMessage(), 
                $ve->getErrors(), 
                $ve->getCode(), 
                $ve->getStatusCode()
            );
        } catch (\Throwable $th) {
            LoggerHelper::emergency('Failed to delete a role.', [
                'error' => $th->getMessage()
            ]);

            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }
}