<?php

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Config\Router;
use Pondra\PhpApiStarterKit\Controllers\HomeController;
use Pondra\PhpApiStarterKit\Controllers\TestController;
use Pondra\PhpApiStarterKit\Jobs\EmailQueueJob;
use Pondra\PhpApiStarterKit\Repositories\EmailQueueRepository;

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../routes/auth.php';
require_once __DIR__ . '/../routes/email.php';
require_once __DIR__ . '/../routes/password.php';
require_once __DIR__ . '/../routes/role.php';

Router::add('GET', '/', HomeController::class, 'index');


$eQCon = new EmailQueueJob();

Router::add('GET', '/test', $eQCon, 'handle');

Router::run();