<?php

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Config\Router;
use Pondra\PhpApiStarterKit\Controllers\RoleController;
use Pondra\PhpApiStarterKit\Middleware\AdminMiddleware;
use Pondra\PhpApiStarterKit\Middleware\AuthMiddleware;
use Pondra\PhpApiStarterKit\Middleware\RateLimitingMiddleware;
use Pondra\PhpApiStarterKit\Repositories\RoleRepository;
use Pondra\PhpApiStarterKit\Services\RoleService;

$connection = Database::getConnection();
$roleRepo = new RoleRepository($connection);
$roleService = new RoleService($roleRepo);
$roleController = new RoleController($roleService);

Router::add('GET', '/api/roles', $roleController, 'index', [
    AuthMiddleware::class, 
    AdminMiddleware::class,
    new RateLimitingMiddleware('GET', 'api_roles')
]);
Router::add('POST', '/api/roles/store', $roleController, 'store', [
    AuthMiddleware::class, 
    AdminMiddleware::class,
    new RateLimitingMiddleware('POST', 'api_roles_store')
]);
Router::add('GET', '/api/roles/([0-9a-zA-Z-]+)/show', $roleController, 'show', [
    AuthMiddleware::class, 
    AdminMiddleware::class,
    new RateLimitingMiddleware('GET', 'api_roles_show')
]);
Router::add('PUT', '/api/roles/([0-9a-zA-Z-]+)/update', $roleController, 'update', [
    AuthMiddleware::class, 
    AdminMiddleware::class,
    new RateLimitingMiddleware('PUT', 'api_roles_update')
]);
Router::add('DELETE', '/api/roles/([0-9a-zA-Z-]+)/delete', $roleController, 'delete', [
    AuthMiddleware::class, 
    AdminMiddleware::class,
    new RateLimitingMiddleware('DELETE', 'api_roles_delete')
]);