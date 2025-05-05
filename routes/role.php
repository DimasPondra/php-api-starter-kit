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

Router::add('POST', '/api/roles/store', $roleController, 'store');