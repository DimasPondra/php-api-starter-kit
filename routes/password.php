<?php

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Config\Router;
use Pondra\PhpApiStarterKit\Controllers\PasswordController;
use Pondra\PhpApiStarterKit\Repositories\PasswordRepository;
use Pondra\PhpApiStarterKit\Services\PasswordService;

$connection = Database::getConnection();
$passwordRepo = new PasswordRepository($connection);
$passwordService = new PasswordService($passwordRepo);
$passwordController = new PasswordController($passwordService);

Router::add('POST', '/api/password/forgot-password', $passwordController, 'forgot');