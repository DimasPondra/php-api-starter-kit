<?php

use Pondra\PhpApiStarterKit\Config\Router;
use Pondra\PhpApiStarterKit\Controllers\HomeController;

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../routes/auth.php';
require_once __DIR__ . '/../routes/email.php';
require_once __DIR__ . '/../routes/role.php';

Router::add('GET', '/', HomeController::class, 'index');

Router::run();