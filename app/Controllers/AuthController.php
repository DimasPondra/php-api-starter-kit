<?php

namespace Pondra\PhpApiStarterKit\Controllers;

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Requests\RegisterRequest;
use Pondra\PhpApiStarterKit\Services\UserService;

class AuthController
{
    private UserService $userService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
    }

    public function register()
    {
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, true);

        $request = new RegisterRequest();
        $request->name = $data['name'] ?? null;
        $request->email = $data['email'] ?? null;
        $request->password = $data['password'] ?? null;

        $this->userService->register($request);
    }
}