<?php

namespace Pondra\PhpApiStarterKit\Controllers;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\AuthHelper;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Requests\LoginRequest;
use Pondra\PhpApiStarterKit\Requests\RegisterRequest;
use Pondra\PhpApiStarterKit\Services\UserService;

class AuthController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register()
    {
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, true);

        $request = new RegisterRequest();
        $request->name = $data['name'] ?? null;
        $request->email = $data['email'] ?? null;
        $request->password = $data['password'] ?? null;

        try {
            $response = $this->userService->register($request);
            
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

    public function login()
    {
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, true);

        $request = new LoginRequest();
        $request->email = $data['email'] ?? null;
        $request->password = $data['password'] ?? null;

        try {
            $response = $this->userService->login($request);
            
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

    public function profile()
    {
        $token = AuthHelper::getToken();

        try {
            $response = $this->userService->getUserFromToken($token);
            
            ResponseHelper::success($response['message'], $response['data']);
        } catch (\Throwable $th) {
            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }

    public function logout()
    {
        $token = AuthHelper::getToken();

        try {
            $response = $this->userService->deleteUserToken($token);
            
            ResponseHelper::success($response['message'], $response['data']);
        } catch (\Throwable $th) {
            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }
}