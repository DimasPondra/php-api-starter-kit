<?php

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Config\Router;
use Pondra\PhpApiStarterKit\Controllers\RoleController;
use Pondra\PhpApiStarterKit\Repositories\RoleRepository;
use Pondra\PhpApiStarterKit\Services\RoleService;

$connection = Database::getConnection();
$roleRepo = new RoleRepository($connection);
$roleService = new RoleService($roleRepo);
$roleController = new RoleController($roleService);

Router::add('GET', '/api/roles/', $roleController, 'index');
Router::add('POST', '/api/roles/store', $roleController, 'store');
Router::add('GET', '/api/roles/([0-9a-zA-Z-]+)/show', $roleController, 'show');
Router::add('PUT', '/api/roles/([0-9a-zA-Z-]+)/update', $roleController, 'update');