<?php

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Config\Router;
use Pondra\PhpApiStarterKit\Controllers\AuthController;
use Pondra\PhpApiStarterKit\Middleware\AuthMiddleware;
use Pondra\PhpApiStarterKit\Middleware\RateLimitingMiddleware;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Services\UserService;

$connection = Database::getConnection();
$userRepo = new UserRepository($connection);
$userService = new UserService($userRepo);
$authController = new AuthController($userService);

Router::add('POST', '/api/auth/register', $authController, 'register', [
    new RateLimitingMiddleware('POST', 'api_auth_regsiter')
]);
Router::add('POST', '/api/auth/login', $authController, 'login', [
    new RateLimitingMiddleware('POST', 'api_auth_login')
]);
Router::add('GET', '/api/auth/profile', $authController, 'profile', [
    AuthMiddleware::class,
    new RateLimitingMiddleware('GET', 'api_auth_profile')
]);
Router::add('GET', '/api/auth/logout', $authController, 'logout', [
    AuthMiddleware::class,
    new RateLimitingMiddleware('GET', 'api_auth_logout')
]);