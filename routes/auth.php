<?php

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Config\Router;
use Pondra\PhpApiStarterKit\Controllers\AuthController;
use Pondra\PhpApiStarterKit\Middleware\AuthMiddleware;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Services\UserService;

$connection = Database::getConnection();
$userRepo = new UserRepository($connection);
$userService = new UserService($userRepo);
$authController = new AuthController($userService);

Router::add('POST', '/api/auth/register', $authController, 'register');
Router::add('POST', '/api/auth/login', $authController, 'login');
Router::add('GET', '/api/auth/profile', $authController, 'profile', [AuthMiddleware::class]);