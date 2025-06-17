<?php

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Config\Router;
use Pondra\PhpApiStarterKit\Controllers\FileController;
use Pondra\PhpApiStarterKit\Controllers\HomeController;
use Pondra\PhpApiStarterKit\Middleware\RateLimitingMiddleware;
use Pondra\PhpApiStarterKit\Repositories\FileRepository;
use Pondra\PhpApiStarterKit\Services\FileService;

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../routes/auth.php';
require_once __DIR__ . '/../routes/email.php';
require_once __DIR__ . '/../routes/password.php';
require_once __DIR__ . '/../routes/role.php';

$connection = Database::getConnection();
$fileRepo = new FileRepository($connection);
$fileService = new FileService($fileRepo);
$fileController = new FileController($fileService);

Router::add('GET', '/', HomeController::class, 'index');

Router::add('POST', '/api/files/upload', $fileController, 'upload', [
    new RateLimitingMiddleware('POST', 'api_files_upload')
]);

Router::run();