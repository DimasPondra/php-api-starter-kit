<?php

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Config\Router;
use Pondra\PhpApiStarterKit\Controllers\EmailController;
use Pondra\PhpApiStarterKit\Middleware\AuthMiddleware;
use Pondra\PhpApiStarterKit\Repositories\EmailRepository;
use Pondra\PhpApiStarterKit\Services\EmailService;

$connection = Database::getConnection();
$emailRepo = new EmailRepository($connection);
$emailService = new EmailService($emailRepo);
$emailController = new EmailController($emailService);

Router::add('POST', '/api/emails/send-verification', $emailController, 'sendVerification', [AuthMiddleware::class]);
Router::add('POST', '/api/emails/verify', $emailController, 'verify');