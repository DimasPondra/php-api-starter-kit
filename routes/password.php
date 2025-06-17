<?php

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Config\Router;
use Pondra\PhpApiStarterKit\Controllers\PasswordController;
use Pondra\PhpApiStarterKit\Middleware\RateLimitingMiddleware;
use Pondra\PhpApiStarterKit\Repositories\PasswordRepository;
use Pondra\PhpApiStarterKit\Services\PasswordService;

$connection = Database::getConnection();
$passwordRepo = new PasswordRepository($connection);
$passwordService = new PasswordService($passwordRepo);
$passwordController = new PasswordController($passwordService);

Router::add('POST', '/api/password/forgot-password', $passwordController, 'forgot', [
    new RateLimitingMiddleware('POST', 'api_password_forgot_password', 3, 86400)
]);
Router::add('POST', '/api/password/reset-password', $passwordController, 'reset', [
    new RateLimitingMiddleware('POST', 'api_password_reset_password', 5, 3600)
]);