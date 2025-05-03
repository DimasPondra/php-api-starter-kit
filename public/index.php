<?php

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Config\Router;
use Pondra\PhpApiStarterKit\Controllers\AuthController;
use Pondra\PhpApiStarterKit\Controllers\HomeController;

require_once __DIR__ . '/../vendor/autoload.php';

Database::getConnection();

Router::add('GET', '/', HomeController::class, 'index');

Router::add('POST', '/api/auth/register', AuthController::class, 'register');

Router::run();